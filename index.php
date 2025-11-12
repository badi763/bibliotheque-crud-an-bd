<?php
require_once __DIR__ . '/config/database.php';

$page_title = "Accueil - Blibliothèque";

$pdo = getDbConnection();

$sql = "SELECT l.id_livre,l.auteur,l.titre,l.couverture, CASE WHEN e.id_emprunt IS NOT NULL THEN 'emprunte' ELSE 'disponible' END as statut FROM livre l LEFT JOIN (SELECT id_livre, id_emprunt FROM emprunt WHERE date_rendu IS NULL) e ON l.id_livre = e.id_livre ORDER BY l.titre ASC;";

//préparation + exécution de la requête
$reqPreparee = $pdo->prepare($sql);
$reqPreparee->execute();

//on récupère tous les résultats dans un tableau associatif
$livres = $reqPreparee->fetchAll();

// echo '<pre>';
// print_r($livres);
// echo '</pre>';


include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';

?>
<!-- Contenu principal de la page -->
<main class="container mx-auto px-4 py-8 flex-grow" role="main">
    <!-- Titre de la page -->
    <header class="mb-8">

        <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>Vous avez été déconnecté avec succès.</p>
            </div>
        <?php endif; ?>

        <h1 class="text-4xl font-bold text-gray-800 mb-2">
            Catalogue de la Bibliothèque
        </h1>
        <p class="text-gray-600">
            Découvrez notre collection de <?= count($livres) ?> livres
        </p>
    </header>

    <!-- Section des livres -->
    <section aria-label="Liste des livres">
        <?php if (empty($livres)): ?>
            <!-- Message si aucun livre n'est disponible -->
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p class="font-bold">Aucun livre disponible</p>
                <p>La bibliothèque ne contient actuellement aucun livre.</p>
            </div>
        <?php else: ?>
            <!-- Grille de livres -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($livres as $livre): ?>
                    <!-- Carte de livre -->
                    <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                        <!-- Image de couverture du livre -->
                        <div class="h-64 bg-gray-200 flex items-center justify-center relative">
                            <!-- Affichage de la couverture si disponible -->
                            <?php if (!empty($livre['couverture'])): ?>
                                <img
                                    src="<?= $livre['couverture']; ?>"
                                    alt="Couverture de <?= $livre['titre']; ?>"
                                    class="h-full w-full object-cover">
                            <?php else: ?>
                                <!-- Placeholder si aucune couverture n'est disponible -->
                                <span class="text-6xl" aria-hidden="true">📖</span>
                            <?php endif; ?>
                            <!-- Étiquette de statut -->
                            <div class="absolute top-2 right-2">
                                <?php if ($livre['statut'] === 'disponible'): ?>
                                    <!-- Badge vert pour les livres disponibles -->
                                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold shadow">
                                        ✓ Disponible
                                    </span>
                                <?php else: ?>
                                    <!-- Badge rouge pour les livres empruntés -->
                                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold shadow">
                                        ✗ En prêt    
                                    </span>
                                <?php endif; ?> 
                            </div>
                        </div>

                        <!-- Informations du livre -->
                        <div class="p-4">
                            <!-- Titre du livre -->
                            <h2 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">
                                <?= htmlspecialchars($livre['titre']); ?>
                            </h2>

                            <!-- Auteur du livre -->
                            <p class="text-gray-600 mb-2">
                                <span class="font-semibold">Auteur :</span>
                                <?= htmlspecialchars($livre['auteur']); ?>
                            </p>

                            <!-- ID du livre -->
                            <p class="text-gray-500 text-sm">
                                Référence : #<?= $livre['id_livre']; ?>
                            </p>
                                <!--badge pour ajouter au panier -->
                               <?php if ($livre['statut'] === 'disponible'): ?> 
    <a href="panier.php?action=add&id=<?= $livre['id_livre']; ?>"
       class="inline-block bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold shadow hover:bg-green-600 transition">
        ➕ Ajouter au panier
    </a>
        <a href="panier.php?action=add&id=<?= $livre['id_livre']; ?>"
       class="inline-block bg-green-500 text-black px-3 py-1 rounded-full text-sm font-semibold shadow hover:bg-green-600 transition">
       emprunter                        
    
       </a>
<?php endif; ?>

                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
<?php
include __DIR__ . '/includes/footer.php';
?>