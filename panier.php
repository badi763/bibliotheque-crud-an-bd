<?php
session_start();
require_once __DIR__ . '/config/database.php';

$page_title = "Votre panier - Bibliothèque";

// Connexion à la base de données
$pdo = getDbConnection();

// Initialiser le panier si vide
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}
// 🛒 Gérer les actions : ajouter, supprimer, vider
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'add' && isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        if (!in_array($id, $_SESSION['panier'])) {
            $_SESSION['panier'][] = $id;
        }
    }

    if ($action === 'remove' && isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        $_SESSION['panier'] = array_diff($_SESSION['panier'], [$id]);
    }

    if ($action === 'clear') {
        $_SESSION['panier'] = [];
    }

    // Redirection pour éviter la répétition de l’action
    header('Location: panier.php');
    exit;
}
if (isset($_POST['commander'])) {
    // Vérifie si l'utilisateur est connecté (abonné)
    if (!isset($_SESSION['user_id'])) {
        // S'il n'est pas connecté, redirige vers la page login
        header('Location: login.php?msg=connectez-vous-pour-commander');
        exit;
    }

    // Vérifie que le panier n’est pas vide
    if (empty($_SESSION['panier'])) {
        header('Location: panier.php?msg=panier-vide');
        exit;
    }

    $_SESSION['panier'] = []; // Vide le panier après la commande
    header('Location: panier.php?msg=commande-validee');
    exit;
}

// 🔍 Récupérer les infos des livres du panier
$livres = [];
if (!empty($_SESSION['panier'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['panier']), '?'));

    $sql = "SELECT * FROM livre WHERE id_livre IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($_SESSION['panier']);

    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main class="container mx-auto px-4 py-8 flex-grow">
    <header class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">🛒 Votre panier</h1>
        <p class="text-gray-600">
            <?= empty($livres) ? 'Votre panier est vide.' : 'Vous avez ' . count($livres) . ' livre(s) dans votre panier.' ?>
        </p>
    </header>

    <?php if (empty($livres)): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
            <p>Vous n’avez ajouté aucun livre pour le moment.</p>
            <a href="index.php" class="text-blue-600 underline">Retourner au catalogue</a>
        </div>
    <?php else: ?>
        <div class="flex justify-end mb-4">
            <a href="panier.php?action=clear" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Vider le panier</a>
        </div>  
        <div class="flex justify-end mt-6">
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="commande.php" 
           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Passer la commande ✅
        </a>
    <?php elseif (!isset ($_SESSION['user_id'])): ?>
        <a href="commande.php?msg=votre commande est passer" 
           class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
            commander 
        </a>
    <?php endif; ?>
</div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($livres as $livre): ?>
                <article class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="h-64 bg-gray-200 flex items-center justify-center">
                        <?php if (!empty($livre['couverture'])): ?>
                            <img src="<?= htmlspecialchars($livre['couverture']); ?>" alt="Couverture de <?= htmlspecialchars($livre['titre']); ?>" class="h-full w-full object-cover">
                        <?php else: ?>
                            <span class="text-6xl">📖</span>
                        <?php endif; ?>
                    
                    </div>
                    <div class="p-4">
                        <h2 class="text-xl font-bold mb-2"><?= htmlspecialchars($livre['titre']); ?></h2>
                        <p class="text-gray-600 mb-1"><strong>Auteur :</strong> <?= htmlspecialchars($livre['auteur']); ?></p>
                        <p class="text-gray-500 text-sm mb-3">Référence : #<?= $livre['id_livre']; ?></p>
                        <a href="panier.php?action=remove&id=<?= $livre['id_livre']; ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">Retirer</a>
                    </div>
                </article>
            <?php endforeach; ?>

        </div>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
