<?php
session_start();
require_once __DIR__ . '/config/database.php';

// ----------------------
// 1️⃣ Vérification de l'utilisateur
// ----------------------
if (!isset($_SESSION['user_id'])) {
    // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: login.php?msg=connectez-vous-pour-commander');
    exit;
}

// ----------------------
// 2️⃣ Vérification du panier
// ----------------------
if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    header('Location: panier.php?msg=panier-vide');
    exit;
}

// ----------------------
// 3️⃣ Connexion à la base
// ----------------------
$pdo = getDbConnection();

// ----------------------
// 4️⃣ Récupération des livres dans le panier
// ----------------------
$idsArray = array_map('intval', $_SESSION['panier']); // s'assure que ce sont des entiers
$$livres = [];

if (!empty($_SESSION['panier'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['panier']), '?'));
    $sql = "SELECT * FROM livre WHERE id_livre IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($_SESSION['panier']);
    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


if (!empty($livres)) {
    try {
        $pdo->beginTransaction();

        // Insérer la commande
        $stmt = $pdo->prepare("INSERT INTO commande (id_abonne, date_commande) VALUES (:id_abonne, NOW())");
        $stmt->bindParam(':id_abonne', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $commande_id = $pdo->lastInsertId();

        // Insérer chaque livre dans commande_livre
        $stmt2 = $pdo->prepare("INSERT INTO commande_livre (id_commande, id_livre) VALUES (:id_commande, :id_livre)");
        foreach ($livres as $livre) {
            $stmt2->execute([
                ':id_commande' => $commande_id,
                ':id_livre' => $livre['id_livre']
            ]);
        }

        $pdo->commit();

        // Vider le panier après la commande
        $_SESSION['panier'] = [];

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erreur lors de la commande : " . $e->getMessage());
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main class="container mx-auto px-4 py-8 flex-grow">
    <header class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">✅ Commande validée</h1>
        <p class="text-gray-600">
            Merci, votre commande a été prise en compte !
        </p>
    </header>

    <section>
        <h2 class="text-2xl font-semibold mb-4">Livres commandés :</h2>
        <?php if (!empty($livres)): ?>
            <ul class="list-disc list-inside">
                <?php foreach ($livres as $livre): ?>
                    <li><?= htmlspecialchars($livre['titre']) ?> - <?= htmlspecialchars($livre['auteur']) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun livre n'a été trouvé dans votre commande.</p>
        <?php endif; ?>
    </section>

    <div class="mt-6">
        <a href="index.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Retour au catalogue</a>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
