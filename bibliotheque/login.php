<?php

//démarrer le ssion : 
session_start();
//appel à la bdd : 
require_once __DIR__ . '/config/database.php';

//si l'utilisateur est déjà connecté, le rediriger vers le tableau de bord admin
if (isset($_SESSION['admin_id'])) {
    header('Location: admin/dashboard.php');
    exit;
}

//page title
$page_title = 'Page de connexion administrateur';

//variable d'affichage
$error = "";

//traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    extract($_POST);
    //vérifier le format de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error .= "<p>Format d'email invalide.</p>";
    }

    //vérifier le mot de passe
    if (iconv_strlen(trim($mot_de_passe)) < 5) {
        $error .= "<p>Le mot de passe doit contenir au moins 5 caractères.</p>";
    }

    if (empty($error)) {
        $pdo = getDbConnection();
        $sql = "SELECT id_admin,login,mot_de_passe,nom,prenom,email 
        FROM administrateur 
        WHERE email = :email LIMIT 1";

        $log = $pdo->prepare($sql);
        $log->bindParam(':email', $email, PDO::PARAM_STR);
        $log->execute();
        // echo $log->rowCount();
        // Vérifier si l'utilisateur existe
        if ($log->rowCount() === 1) {
            $admin = $log->fetch(PDO::FETCH_ASSOC);
            // echo '<pre>';
            // print_r($admin);
            // echo '</pre>';

            // // Vérifier le mot de passe
            if (password_verify($mot_de_passe, $admin['mot_de_passe'])) {
                // Authentification réussie => création de la session 
                $_SESSION['admin_id']   = $admin['id_admin'];
                $_SESSION['admin_login'] = $admin['login'];
                $_SESSION['admin_nom'] = $admin['nom'];
                $_SESSION['admin_prenom'] = $admin['prenom'];
                $_SESSION['admin_email'] = $admin['email'];

                //mettre à jour la date de dernière connexion
                $update_sql = "UPDATE administrateur SET dernier_acces = NOW() WHERE id_admin = :id_admin";
                $update_value = $pdo->prepare($update_sql);
                $update_value->bindParam(':id_admin', $admin['id_admin'], PDO::PARAM_INT);
                $update_value->execute();

                //message de succès : 
                $_SESSION['message'] = 'Bienvenue ' . $admin['prenom'] . '';
                //redirection vers le tableau de bord admin
                header('Location: admin/dashboard.php');
                exit;
            } else {
                $error .= "<p>Email ou mot de passe incorrect.</p>";
            }
        } else {
            $error .= "<p>Email ou mot de passe incorrect.</p>";
        }
    }
}

require_once __DIR__ . '/config/database.php';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';

?>

<!-- Contenu principal de la page -->
<main class="container mx-auto px-4 py-8 flex-grow" role="main">
    <div class="max-w-md mx-auto">
        <!-- Titre de la page -->
        <header class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                Connexion Administrateur
            </h1>
            <p class="text-gray-600">
                Connectez-vous pour accéder à l'interface d'administration
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
                        Email
                    </label>
                    <input
                        type="text"
                        id="email"
                        name="email"
                        required
                        autocomplete="email"
                        value="<?= $_POST['email'] ?? ""; ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Entrez votre email">
                </div>

                <!-- Champ Mot de passe -->
                <div class="mb-6">
                    <label for="mot_de_passe" class="block text-gray-700 font-semibold mb-2">
                        Mot de passe
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

            <!-- Lien de retour -->
            <div class="mt-6 text-center">
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