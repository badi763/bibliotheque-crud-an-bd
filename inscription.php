<?php
// Démarrer la session
session_start();

// Appel à la BDD
require_once __DIR__ . '/config/database.php';

// Page title
$page_title = 'Inscription - Nouvel abonné';

// Variables d'affichage
$errors = [];
$success = false;
$nom = '';
$prenom = '';
$email = '';

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$civilite = trim($_POST['civilite'] ?? '');
$nom = trim($_POST['Nom'] ?? '');
$prenom = trim($_POST['Prenom'] ?? '');
$email = trim($_POST['email'] ?? '');
$mot_de_passe = $_POST['mot_de_passe'] ?? '';
$mot_de_passe_verif = $_POST['mot_de_passe_verif'] ?? '';

// Validation de la civilité
if (empty($civilite)) {
    $errors[] = "La civilité est obligatoire.";
}

// ... (tes autres validations ici)

// Si aucune erreur
if (empty($errors)) {
    try {
        $pdo = getDBConnection();

        // Hash du mot de passe
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // ✅ Inclure civilite dans la requête SQL
        $sql = "INSERT INTO abonne (civilite, nom, prenom, email, mot_de_passe)
                VALUES (:civilite, :nom, :prenom, :email, :mot_de_passe)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':civilite', $civilite, PDO::PARAM_STR);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe_hash, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $success = true;
            $_SESSION['abonne_id'] = $pdo->lastInsertId();
            $_SESSION['abonne_nom'] = $nom;
            $_SESSION['abonne_prenom'] = $prenom;
            $_SESSION['abonne_email'] = $email;

            header("refresh:2;url=index.php");
        }
    } catch (PDOException $e) {
        $errors[] = "Erreur lors de l'inscription : " . $e->getMessage();
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
                Inscription
            </h1>
            <p class="text-gray-600">
                Créez votre compte pour emprunter des livres
            </p>
        </header>

        <!-- Formulaire d'inscription -->
        <section class="bg-white rounded-lg shadow-md p-8">
            
            <!-- Affichage du message de succès -->
            <?php if ($success): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">✓ Inscription réussie !</p>
                    <p>Votre compte a été créé avec succès. Redirection en cours...</p>
                </div>
            <?php endif; ?>

            <!-- Affichage des erreurs -->
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

            <!-- Formulaire -->
            <form method="POST" action="<?= $_SERVER['PHP_SELF']; ?>" novalidate>
                <!--civilite-->
                <div class="mb-6">
  <label for="civilite" class="block text-gray-700 font-semibold mb-2">
      Civilité
  </label>
  <input
      type="text"
      id="civilite"
      name="civilite"
      required
      value="<?= $_POST['civilite'] ?? ''; ?>"
      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
      placeholder="Ex : M. ou Mme">
</div>

                <!-- Champ Nom -->
                <div class="mb-6">
                    <label for="Nom" class="block text-gray-700 font-semibold mb-2">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="Nom"
                        name="Nom"
                        required
                        autocomplete="family-name"
                        value="<?= htmlspecialchars($nom); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Entrez votre nom">
                </div>

                <!-- Champ Prénom -->
                <div class="mb-6">
                    <label for="Prenom" class="block text-gray-700 font-semibold mb-2">
                        Prénom <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="Prenom"
                        name="Prenom"
                        required
                        autocomplete="given-name"
                        value="<?= htmlspecialchars($prenom); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Entrez votre prénom">
                </div>

                <!-- Champ Email -->
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
                        value="<?= htmlspecialchars($email); ?>"
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
                        autocomplete="new-password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Entrez votre mot de passe">
                    <p class="text-gray-500 text-sm mt-1">Minimum 5 caractères</p>
                </div>

                <!-- Champ Vérification Mot de passe -->
                <div class="mb-6">
                    <label for="mot_de_passe_verif" class="block text-gray-700 font-semibold mb-2">
                        Vérifiez votre mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        id="mot_de_passe_verif"
                        name="mot_de_passe_verif"
                        required
                        autocomplete="new-password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Confirmez votre mot de passe">
                </div>

                <!-- Légende des champs obligatoires -->
                <p class="text-gray-600 text-sm mb-6">
                    <span class="text-red-500">*</span> Champs obligatoires
                </p>

                <!-- Bouton de soumission -->
                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition">
                    S'inscrire
                </button>
            </form>

            <!-- Lien vers la page de connexion -->
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Vous avez déjà un compte ? 
                    <a href="/bibliotheque/login.php" class="text-blue-600 hover:text-blue-800 transition font-semibold">
                        Connectez-vous
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
