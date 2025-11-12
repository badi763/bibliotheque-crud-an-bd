<?php
// Démarrer la session
session_start();

// Vérifier que l'utilisateur est un abonné connecté
if (!isset($_SESSION['abonne_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/config/database.php';
$pdo = getDBConnection();

// Variables
$errors = [];
$success_message = '';
$nom = '';
$prenom = '';
$email = '';

// Récupérer les informations actuelles de l'abonné
$sql = "SELECT nom, prenom, email FROM abonne WHERE id_abonne = :id_abonne";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_abonne', $_SESSION['abonne_id'], PDO::PARAM_INT);
$stmt->execute();
$abonne = $stmt->fetch(PDO::FETCH_ASSOC);

if ($abonne) {
    $nom = $abonne['nom'];
    $prenom = $abonne['prenom'];
    $email = $abonne['email'];
}

// Traitement de la modification du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $ancien_mot_de_passe = $_POST['ancien_mot_de_passe'] ?? '';
    $nouveau_mot_de_passe = $_POST['nouveau_mot_de_passe'] ?? '';
    $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe'] ?? '';

    // Validation
    if (empty($nom)) {
        $errors[] = "Le nom est obligatoire.";
    }
    if (empty($prenom)) {
        $errors[] = "Le prénom est obligatoire.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide.";
    }

    // Vérifier si l'email existe déjà (autre que le sien)
    $check_email = $pdo->prepare("SELECT id_abonne FROM abonne WHERE email = :email AND id_abonne != :id_abonne");
    $check_email->execute([':email' => $email, ':id_abonne' => $_SESSION['abonne_id']]);
    if ($check_email->rowCount() > 0) {
        $errors[] = "Cet email est déjà utilisé par un autre compte.";
    }

    // Si modification du mot de passe
    if (!empty($nouveau_mot_de_passe)) {
        // Vérifier l'ancien mot de passe
        $check_pass = $pdo->prepare("SELECT mot_de_passe FROM abonne WHERE id_abonne = :id_abonne");
        $check_pass->execute([':id_abonne' => $_SESSION['abonne_id']]);
        $user = $check_pass->fetch();

        if (!password_verify($ancien_mot_de_passe, $user['mot_de_passe'])) {
            $errors[] = "L'ancien mot de passe est incorrect.";
        }

        if (strlen($nouveau_mot_de_passe) < 5) {
            $errors[] = "Le nouveau mot de passe doit contenir au moins 5 caractères.";
        }

        if ($nouveau_mot_de_passe !== $confirmer_mot_de_passe) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
    }

    // Si pas d'erreurs, mise à jour
    if (empty($errors)) {
        try {
            if (!empty($nouveau_mot_de_passe)) {
                $mot_de_passe_hash = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);
                $update_sql = "UPDATE abonne SET nom = :nom, prenom = :prenom, email = :email, mot_de_passe = :mot_de_passe WHERE id_abonne = :id_abonne";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->execute([
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':email' => $email,
                    ':mot_de_passe' => $mot_de_passe_hash,
                    ':id_abonne' => $_SESSION['abonne_id']
                ]);
            } else {
                $update_sql = "UPDATE abonne SET nom = :nom, prenom = :prenom, email = :email WHERE id_abonne = :id_abonne";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->execute([
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':email' => $email,
                    ':id_abonne' => $_SESSION['abonne_id']
                ]);
            }

            // Mettre à jour la session
            $_SESSION['abonne_nom'] = $nom;
            $_SESSION['abonne_prenom'] = $prenom;
            $_SESSION['abonne_email'] = $email;

            $success_message = "Vos informations ont été mises à jour avec succès !";
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}

// Traitement de la suppression du compte
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer'])) {
    $mot_de_passe_suppression = $_POST['mot_de_passe_suppression'] ?? '';

    // Vérifier le mot de passe
    $check_pass = $pdo->prepare("SELECT mot_de_passe FROM abonne WHERE id_abonne = :id_abonne");
    $check_pass->execute([':id_abonne' => $_SESSION['abonne_id']]);
    $user = $check_pass->fetch();

    if (password_verify($mot_de_passe_suppression, $user['mot_de_passe'])) {
        // Vérifier s'il y a des emprunts en cours
        $check_emprunts = $pdo->prepare("SELECT COUNT(*) as nb FROM emprunt WHERE id_abonne = :id_abonne AND date_rendu IS NULL");
        $check_emprunts->execute([':id_abonne' => $_SESSION['abonne_id']]);
        $result = $check_emprunts->fetch();

        if ($result['nb'] > 0) {
            $errors[] = "Impossible de supprimer votre compte : vous avez encore des livres empruntés. Veuillez les retourner d'abord.";
        } else {
            try {
                // Supprimer le compte
                $delete_sql = "DELETE FROM abonne WHERE id_abonne = :id_abonne";
                $delete_stmt = $pdo->prepare($delete_sql);
                $delete_stmt->execute([':id_abonne' => $_SESSION['abonne_id']]);

                // Détruire la session
                session_destroy();

                // Redirection vers l'accueil avec message
                header('Location: index.php?message=compte_supprime');
                exit();
            } catch (PDOException $e) {
                $errors[] = "Erreur lors de la suppression : " . $e->getMessage();
            }
        }
    } else {
        $errors[] = "Mot de passe incorrect.";
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main class="container mx-auto px-4 py-8 flex-grow" role="main">
    <div class="max-w-3xl mx-auto">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Mon profil</h1>
            <p class="text-gray-600">Gérez vos informations personnelles</p>
        </header>

        <!-- Messages -->
        <?php if ($success_message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p class="font-bold">✓ Succès</p>
                <p><?= htmlspecialchars($success_message) ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Erreur(s) :</p>
                <ul class="list-disc list-inside mt-2">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Formulaire de modification -->
        <section class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Modifier mes informations</h2>
            
            <form method="POST" action="" novalidate>
                <div class="mb-6">
                    <label for="nom" class="block text-gray-700 font-semibold mb-2">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($nom) ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-6">
                    <label for="prenom" class="block text-gray-700 font-semibold mb-2">
                        Prénom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="prenom" name="prenom" required value="<?= htmlspecialchars($prenom) ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-6">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <hr class="my-8">

                <h3 class="text-xl font-bold text-gray-800 mb-4">Changer de mot de passe (optionnel)</h3>

                <div class="mb-6">
                    <label for="ancien_mot_de_passe" class="block text-gray-700 font-semibold mb-2">
                        Ancien mot de passe
                    </label>
                    <input type="password" id="ancien_mot_de_passe" name="ancien_mot_de_passe"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-6">
                    <label for="nouveau_mot_de_passe" class="block text-gray-700 font-semibold mb-2">
                        Nouveau mot de passe
                    </label>
                    <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-gray-500 text-sm mt-1">Minimum 5 caractères</p>
                </div>

                <div class="mb-6">
                    <label for="confirmer_mot_de_passe" class="block text-gray-700 font-semibold mb-2">
                        Confirmer le nouveau mot de passe
                    </label>
                    <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit" name="modifier"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition">
                    ✓ Enregistrer les modifications
                </button>
            </form>
        </section>

        <!-- Section suppression de compte -->
        <section class="bg-red-50 border border-red-200 rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-red-800 mb-4">Zone dangereuse</h2>
            <p class="text-gray-700 mb-6">
                La suppression de votre compte est <strong>définitive et irréversible</strong>. 
                Toutes vos données seront supprimées.
            </p>

            <form method="POST" action="" novalidate onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">
                <div class="mb-6">
                    <label for="mot_de_passe_suppression" class="block text-gray-700 font-semibold mb-2">
                        Confirmez votre mot de passe pour supprimer votre compte
                    </label>
                    <input type="password" id="mot_de_passe_suppression" name="mot_de_passe_suppression" required
                        class="w-full px-4 py-2 border border-red-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <button type="submit" name="supprimer"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition">
                    🗑️ Supprimer définitivement mon compte
                </button>
            </form>
        </section>
    </div>
</main>

<?php
include __DIR__ . '/includes/footer.php';
?>