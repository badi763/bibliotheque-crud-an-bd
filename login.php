<?php
// Démarrer la session
session_start();

// Appel à la BDD
require_once __DIR__ . '/./config/database.php';

// Si l'utilisateur est déjà connecté, le rediriger
if (isset($_SESSION['admin_id'])) {
    header('Location: admin/dashboard.php');
    exit;
}

if (isset($_SESSION['abonne_id'])) {
    header('Location: index.php');
    exit;
}

// Page title
$page_title = 'Page de connexion';

// Variable d'affichage
$error = "";

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //crée des variables à partir des clés du tableau associatif passé en paramètre
    extract($_POST);
    
    // Vérifier le format de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error .= "<p>Format d'email invalide.</p>";
    }

    // Vérifier le mot de passe
    if (iconv_strlen(trim($mot_de_passe)) < 5) {
        $error .= "<p>Le mot de passe doit contenir au moins 5 caractères.</p>";
    }

    if (empty($error)) {
        $pdo = getDBConnection();
        
        // D'abord, chercher dans la table administrateur
        $sql_admin = "SELECT id_admin, login, mot_de_passe, nom, prenom, email 
                      FROM administrateur 
                      WHERE email = :email LIMIT 1";

        $stmt_admin = $pdo->prepare($sql_admin);
        $stmt_admin->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt_admin->execute();

        if ($stmt_admin->rowCount() === 1) {
            // Utilisateur trouvé dans administrateur
            $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);
            
            // Vérifier le mot de passe
            if (password_verify($mot_de_passe, $admin['mot_de_passe'])) {
                // Authentification réussie en tant qu'admin
                $_SESSION['admin_id'] = $admin['id_admin'];
                $_SESSION['admin_login'] = $admin['login'];
                $_SESSION['admin_nom'] = $admin['nom'];
                $_SESSION['admin_prenom'] = $admin['prenom'];
                $_SESSION['admin_email'] = $admin['email'];

                // Mettre à jour la date de dernière connexion
                $update_sql = "UPDATE administrateur SET dernier_acces = NOW() WHERE id_admin = :id_admin";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->bindParam(':id_admin', $admin['id_admin'], PDO::PARAM_INT);
                $update_stmt->execute();

                // Message de succès
                $_SESSION['message'] = 'Bienvenue ' . $admin['prenom'] . '';
                
                // Redirection vers le tableau de bord admin
                header('Location: admin/dashboard.php');
                exit;
            } else {
                $error .= "<p>Email ou mot de passe incorrect.</p>";
            }
        } else {
            // Si pas trouvé dans administrateur, chercher dans abonne
            $sql_abonne = "SELECT id_abonne, nom, prenom, email, mot_de_passe 
                           FROM abonne 
                           WHERE email = :email LIMIT 1";

            $stmt_abonne = $pdo->prepare($sql_abonne);
            $stmt_abonne->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt_abonne->execute();

            if ($stmt_abonne->rowCount() === 1) {
                // Utilisateur trouvé dans abonne
                $abonne = $stmt_abonne->fetch(PDO::FETCH_ASSOC);
                
                // Vérifier le mot de passe
                if (password_verify($mot_de_passe, $abonne['mot_de_passe'])) {
                    // Authentification réussie en tant qu'abonné
                    $_SESSION['abonne_id'] = $abonne['id_abonne'];
                    $_SESSION['abonne_nom'] = $abonne['nom'];
                    $_SESSION['abonne_prenom'] = $abonne['prenom'];
                    $_SESSION['abonne_email'] = $abonne['email'];

                    // Message de succès
                    $_SESSION['message'] = 'Bienvenue ' . $abonne['prenom'] . ' !';
                    
                    // Redirection vers l'accueil
                    header('Location: index.php');
                    exit;
                } else {
                    $error .= "<p>Email ou mot de passe incorrect.</p>";
                }
            } else {
                $error .= "<p>Email ou mot de passe incorrect.</p>";
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<!-- Contenu principal de la page -->
<main class="container mx-auto px-4 py-8 flex-grow" role="main">
    <div class="max-w-md mx-auto">
        <!-- Titre de la page -->
        <header class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                Connexion
            </h1>
            <p class="text-gray-600">
                Connectez-vous à votre compte
            </p>
        </header>

        <!-- Formulaire de connexion -->
        <section class="bg-white rounded-lg shadow-md p-8">
            <!-- Affichage des erreurs -->
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Erreur</p>
                    <p><?= $error; ?></p>
                </div>
            <?php endif; ?>

            <!-- Formulaire -->
            <form method="POST" action="<?= $_SERVER['PHP_SELF']; ?>" novalidate>
                <!-- Champ email -->
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        autocomplete="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? ""); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Entrez votre email">
                </div>

                <!-- Champ Mot de passe -->
                <div class="mb-6">
                    <label for="mot_de_passe" class="block text-gray-700 font-semibold mb-2">
                        Mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        id="mot_de_passe"
                        name="mot_de_passe"
                        required
                        autocomplete="current-password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Entrez votre mot de passe">
                </div>

                <!-- Bouton de soumission -->
                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition">
                    Se connecter
                </button>
            </form>

            <!-- Lien vers l'inscription -->
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Vous n'avez pas de compte ? 
                    <a href="/bibliotheque/inscription.php" class="text-blue-600 hover:text-blue-800 transition font-semibold">
                        Inscrivez-vous
                    </a>
                </p>
            </div>

            <!-- Lien de retour -->
            <div class="mt-4 text-center">
                <a href="/bibliotheque/index.php" class="text-blue-600 hover:text-blue-800 transition">
                    ← Retour à l'accueil
                </a>
            </div>
        </section>
    </div>
</main>

<?php
include __DIR__ . '/includes/footer.php';
?>
