<?php
require_once __DIR__ . '/../config/database.php';

// Récupération de la connexion à la base de données
$pdo = getDBConnection();

// Démarrage de la session pour les messages
session_start();

// Variables pour les messages
$erreurs = [];
$livre = null;
$id_livre = null;
$message_succes = '';

// Récupération du message de succès depuis la session
if (isset($_SESSION['message_succes'])) {
    $message_succes = $_SESSION['message_succes'];
    unset($_SESSION['message_succes']); // Supprime le message après affichage
}

// Vérification de la présence de l'ID dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $erreurs[] = "Aucun livre spécifié pour la modification.";
} else {
    $id_livre = (int)$_GET['id'];
    
    // Récupération des informations du livre à modifier
    $sql = "SELECT id_livre, titre, auteur, couverture 
            FROM livre 
            WHERE id_livre = :id_livre";
    
    $reqPreparee = $pdo->prepare($sql);
    $reqPreparee->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
    $reqPreparee->execute();
    $livre = $reqPreparee->fetch();

    // Vérification que le livre existe
    if (!$livre) {
        $erreurs[] = "Le livre demandé n'existe pas dans la base de données.";
    }
}

// Traitement du formulaire si soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $livre) {
    
    // Récupération et nettoyage des données du formulaire
    $titre = trim($_POST['titre'] ?? '');
    $auteur = trim($_POST['auteur'] ?? '');
    $couverture = trim($_POST['couverture'] ?? '');
    
    // Validation des données
    if (empty($titre)) {
        $erreurs[] = "Le titre du livre est obligatoire.";
    } elseif (strlen($titre) > 30) {
        $erreurs[] = "Le titre ne doit pas dépasser 30 caractères.";
    }
    
    if (empty($auteur)) {
        $erreurs[] = "L'auteur du livre est obligatoire.";
    } elseif (strlen($auteur) > 25) {
        $erreurs[] = "L'auteur ne doit pas dépasser 25 caractères.";
    }
    
    if (!empty($couverture)) {
        if (strlen($couverture) > 100) {
            $erreurs[] = "L'URL de la couverture ne doit pas dépasser 100 caractères.";
        }
        
        // Vérification de l'extension d'image
        $extensions_valides = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
        $extension = strtolower(pathinfo($couverture, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $extensions_valides)) {
            $erreurs[] = "L'URL de la couverture doit avoir une extension d'image valide (.jpg, .jpeg, .png, .gif, .webp, .svg).";
        }
    }
    
    // Si pas d'erreurs, mise à jour du livre
    if (empty($erreurs)) {
        try {
            $sqlUpdate = "UPDATE livre 
                         SET titre = :titre, 
                             auteur = :auteur, 
                             couverture = :couverture 
                         WHERE id_livre = :id_livre";
            
            $reqUpdate = $pdo->prepare($sqlUpdate);
            $reqUpdate->bindParam(':titre', $titre, PDO::PARAM_STR);
            $reqUpdate->bindParam(':auteur', $auteur, PDO::PARAM_STR);
            $reqUpdate->bindParam(':couverture', $couverture, PDO::PARAM_STR);
            $reqUpdate->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
            $reqUpdate->execute();
            
            // Stockage du message de succès dans la session
            $_SESSION['message_succes'] = "Le livre a été modifié avec succès !";
            
            // Redirection vers la même page pour afficher le message
            header('Location: livre_edit.php?id=' . $id_livre);
            exit;
            
        } catch (Exception $e) {
            $erreurs[] = "Erreur lors de la modification du livre : " . $e->getMessage();
        }
    } else {
        // En cas d'erreurs, on conserve les valeurs saisies
        $livre['titre'] = $titre;
        $livre['auteur'] = $auteur;
        $livre['couverture'] = $couverture;
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/nav.php';
?>

<!-- Contenu principal de la page -->
<main class="container mx-auto px-4 py-8 flex-grow" role="main">
    <div class="max-w-2xl mx-auto">
        <!-- En-tête de la page -->
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                Modifier un livre
            </h1>
            <?php if ($livre): ?>
            <p class="text-gray-600">
                Livre ID : <?= $livre['id_livre'] ?>
            </p>
            <?php endif; ?>
        </header>

        <!-- Message de succès -->
        <?php if (!empty($message_succes)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p class="font-bold">✓ Succès</p>
                <p><?= htmlspecialchars($message_succes) ?></p>
            </div>
        <?php endif; ?>

        <!-- Affichage des erreurs -->
        <?php if (!empty($erreurs)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Erreur(s) :</p>
                <ul class="list-disc list-inside mt-2">
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?= htmlspecialchars($erreur) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <?php if (!$livre): ?>
                <!-- Bouton de retour si le livre n'existe pas -->
                <div class="text-center">
                    <a href="livres.php" 
                       class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition">
                        ← Retour à la liste
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($livre): ?>
        <!-- Formulaire de modification -->
        <section class="bg-white rounded-lg shadow-md p-8">
            <!-- Formulaire -->
            <form method="POST" action="" novalidate>
                <!-- Champ Titre -->
                <div class="mb-6">
                    <label for="titre" class="block text-gray-700 font-semibold mb-2">
                        Titre du livre <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="titre"
                        name="titre"
                        required
                        maxlength="30"
                        value="<?= htmlspecialchars($livre['titre']) ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ex: Les Misérables">
                    <p class="text-gray-500 text-sm mt-1">Maximum 30 caractères</p>
                </div>

                <!-- Champ Auteur -->
                <div class="mb-6">
                    <label for="auteur" class="block text-gray-700 font-semibold mb-2">
                        Auteur <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="auteur"
                        name="auteur"
                        required
                        maxlength="25"
                        value="<?= htmlspecialchars($livre['auteur']) ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ex: VICTOR HUGO">
                    <p class="text-gray-500 text-sm mt-1">Maximum 25 caractères</p>
                </div>

                <!-- Champ Couverture -->
                <div class="mb-6">
                    <label for="couverture" class="block text-gray-700 font-semibold mb-2">
                        URL de la couverture
                    </label>
                    <input
                        type="text"
                        id="couverture"
                        name="couverture"
                        maxlength="100"
                        value="<?= htmlspecialchars($livre['couverture'] ?? '') ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ex: images/couvertures/les_miserables.jpg">
                    <p class="text-gray-500 text-sm mt-1">Optionnel - Chemin relatif vers l'image de couverture</p>
                </div>

                <!-- Légende des champs obligatoires -->
                <p class="text-gray-600 text-sm mb-6">
                    <span class="text-red-500">*</span> Champs obligatoires
                </p>

                <!-- Boutons d'action -->
                <div class="flex justify-between items-center">
                    <!-- Bouton Annuler -->
                    <a href="livres.php" class="text-gray-600 hover:text-gray-800 transition font-medium">
                        ← Annuler
                    </a>

                    <!-- Bouton Enregistrer -->
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition shadow">
                        ✓ Enregistrer les modifications
                    </button>
                </div>
            </form>
        </section>
        <?php endif; ?>
    </div>
</main>

<!-- Pied de page -->
<?php include __DIR__ . '/../includes/footer.php'; ?>