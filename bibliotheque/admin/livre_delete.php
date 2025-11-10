<?php
require_once __DIR__ . '/../config/database.php';

// Récupération de la connexion à la base de données
$pdo = getDBConnection();

// Variables pour les messages
$erreur = '';
$livre = null;
$id_livre = null;

// Vérification de la présence de l'ID dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $erreur = "Aucun livre spécifié pour la suppression.";
} else {
    $id_livre = (int)$_GET['id'];
    
    // Récupération des informations du livre à supprimer
    $sql = "SELECT 
                l.id_livre,
                l.titre,
                l.auteur,
                CASE WHEN e.id_emprunt IS NOT NULL THEN 'emprunte' ELSE 'disponible' END as statut
            FROM livre l 
            LEFT JOIN (
                SELECT id_livre, id_emprunt 
                FROM emprunt 
                WHERE date_rendu IS NULL
            ) e ON l.id_livre = e.id_livre
            WHERE l.id_livre = :id_livre";

    $reqPreparee = $pdo->prepare($sql);
    $reqPreparee->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
    $reqPreparee->execute();
    $livre = $reqPreparee->fetch();

    // Vérification que le livre existe
    if (!$livre) {
        $erreur = "Le livre demandé n'existe pas dans la base de données.";
    }
}

// Traitement de la suppression si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $livre) {
    
    // Vérification que le livre n'est pas actuellement emprunté
    if ($livre['statut'] === 'emprunte') {
        $erreur = "Impossible de supprimer ce livre car il est actuellement emprunté. Veuillez d'abord enregistrer son retour.";
    } else {
        try {
            // Début de la transaction
            $pdo->beginTransaction();
            
            // Suppression des emprunts historiques liés à ce livre
            $sqlDeleteEmprunts = "DELETE FROM emprunt WHERE id_livre = :id_livre";
            $reqDeleteEmprunts = $pdo->prepare($sqlDeleteEmprunts);
            $reqDeleteEmprunts->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
            $reqDeleteEmprunts->execute();
            
            // Suppression du livre
            $sqlDelete = "DELETE FROM livre WHERE id_livre = :id_livre";
            $reqDelete = $pdo->prepare($sqlDelete);
            $reqDelete->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
            $reqDelete->execute();
            
            // Validation de la transaction
            $pdo->commit();
            
            // Redirection vers la liste avec un message de succès
            header('Location: livres.php?message=delete_success');
            exit;
            
        } catch (Exception $e) {
            // Annulation de la transaction en cas d'erreur
            $pdo->rollBack();
            $erreur = "Erreur lors de la suppression du livre : " . $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/nav.php';
?>

<!-- Contenu principal de la page -->
<main class="container mx-auto px-4 py-8 flex-grow" role="main">
    
    <!-- En-tête de la page -->
    <header class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">
            Supprimer un livre
        </h1>
        <p class="text-gray-600">
            Confirmez la suppression du livre
        </p>
    </header>

    <!-- Section du formulaire de suppression -->
    <section class="max-w-2xl mx-auto">
        
        <!-- Affichage des erreurs -->
        <?php if (!empty($erreur)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">❌ Erreur</p>
                <p><?= htmlspecialchars($erreur) ?></p>
            </div>
            
            <!-- Bouton de retour en cas d'erreur -->
            <div class="text-center">
                <a href="livres.php" 
                   class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition">
                    ← Retour à la liste
                </a>
            </div>
        <?php endif; ?>

        <?php if ($livre): ?>
            <!-- Carte d'information du livre -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="border-l-4 border-red-500 pl-4 mb-4">
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">
                        ⚠️ Attention : Suppression définitive
                    </h2>
                    <p class="text-gray-600">
                        Cette action est irréversible. Le livre sera définitivement supprimé de la base de données.
                    </p>
                </div>

                <!-- Informations du livre -->
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex">
                        <span class="font-semibold text-gray-700 w-32">ID :</span>
                        <span class="text-gray-900"><?= $livre['id_livre'] ?></span>
                    </div>
                    <div class="flex">
                        <span class="font-semibold text-gray-700 w-32">Titre :</span>
                        <span class="text-gray-900"><?= htmlspecialchars($livre['titre']) ?></span>
                    </div>
                    <div class="flex">
                        <span class="font-semibold text-gray-700 w-32">Auteur :</span>
                        <span class="text-gray-900"><?= htmlspecialchars($livre['auteur']) ?></span>
                    </div>
                    <div class="flex">
                        <span class="font-semibold text-gray-700 w-32">Statut :</span>
                        <?php if ($livre['statut'] === 'disponible'): ?>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                ✓ Disponible
                            </span>
                        <?php else: ?>
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">
                                ✗ En prêt
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Message d'avertissement si le livre est emprunté -->
                <?php if ($livre['statut'] === 'emprunte'): ?>
                    <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 mt-4" role="alert">
                        <p class="font-bold">⚠️ Livre actuellement emprunté</p>
                        <p>Ce livre ne peut pas être supprimé car il est actuellement en prêt. Veuillez d'abord enregistrer son retour.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Formulaire de confirmation -->
            <form method="POST" class="bg-white rounded-lg shadow-lg p-6">
                
                <!-- Boutons d'action -->
                <div class="flex justify-between items-center gap-4">
                    <!-- Bouton Annuler -->
                    <a href="livres.php" 
                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition text-center">
                        ← Annuler
                    </a>

                    <!-- Bouton Supprimer -->
                    <button type="submit" 
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition <?= $livre['statut'] === 'emprunte' ? 'opacity-50 cursor-not-allowed' : '' ?>"
                            <?= $livre['statut'] === 'emprunte' ? 'disabled' : '' ?>
                            onclick="return confirm('⚠️ CONFIRMATION FINALE\n\nÊtes-vous absolument sûr de vouloir supprimer ce livre ?\n\nTitre : <?= addslashes($livre['titre']) ?>\nAuteur : <?= addslashes($livre['auteur']) ?>\n\nCette action est IRRÉVERSIBLE.');">
                        🗑️ Confirmer la suppression
                    </button>
                </div>

                <?php if ($livre['statut'] === 'emprunte'): ?>
                    <p class="text-sm text-gray-500 text-center mt-4">
                        Le bouton de suppression est désactivé car le livre est actuellement emprunté.
                    </p>
                <?php endif; ?>
            </form>
        <?php endif; ?>

    </section>

</main>

<!-- Pied de page -->
<?php include __DIR__ . '/../includes/footer.php'; ?>