<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Site Bibliothèque</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <meta name="description" content="Système de gestion de bibliothèque">
    <meta name="author" content="Bibliothèque">
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
        <p>MESSAGE SUCCESS</p>
    </div>

    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
        <p>MESSAGE ERREUR</p>
    </div>
    <!-- Navigation principale -->
    <nav class="bg-blue-600 text-white shadow-lg" role="navigation" aria-label="Navigation principale">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <!-- Logo et titre -->
                <div class="flex items-center">
                    <a href="/bibliotheque/index.php" class="text-2xl font-bold hover:text-blue-200 transition">
                        📚 Bibliothèque
                    </a>
                </div>

                <!-- Menu de navigation -->
                <ul class="flex items-center space-x-6">
                    <!-- Lien vers l'accueil -->
                    <li>
                        <a href="/bibliotheque/index.php" class="hover:text-blue-200 transition font-medium">
                            Accueil
                        </a>
                    </li>

                    <!-- Liens visibles uniquement pour les administrateurs connectés -->
                    <li>
                        <a href="/bibliotheque/admin/dashboard.php" class="hover:text-blue-200 transition font-medium">
                            Tableau de bord
                        </a>
                    </li>
                    <li>
                        <a href="/bibliotheque/admin/livres.php" class="hover:text-blue-200 transition font-medium">
                            Gérer les livres
                        </a>
                    </li>
                    <li>
                        <!-- Affichage du nom de l'administrateur connecté -->
                        <span class="text-blue-200">
                            Bonjour, Jean-Michel
                        </span>
                    </li>
                    <li>
                        <!-- Bouton de déconnexion -->
                        <a href="/bibliotheque/logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition font-medium">
                            Déconnexion
                        </a>
                    </li>
                    <!-- Lien visible uniquement pour les visiteurs non connectés -->
                    <li>
                        <a href="/bibliotheque/login.php" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded transition font-medium">
                            Connexion Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal de la page -->
    <main class="container mx-auto px-4 py-8 flex-grow" role="main">
        <!-- Titre de la page -->
        <header class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                Catalogue de la Bibliothèque
            </h1>
            <p class="text-gray-600">
                Découvrez notre collection de 45 livres
            </p>
        </header>

        <!-- Section des livres -->
        <section aria-label="Liste des livres">
            <!-- Message si aucun livre n'est disponible -->
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p class="font-bold">Aucun livre disponible</p>
                <p>La bibliothèque ne contient actuellement aucun livre.</p>
            </div>
            <!-- Grille de livres -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Carte de livre -->
                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <!-- Image de couverture du livre -->
                    <div class="h-64 bg-gray-200 flex items-center justify-center relative">
                        <!-- Affichage de la couverture si disponible -->
                        <img
                            src="images/couvertures/pere_goriot.jpg"
                            alt="Couverture de Père Goriot"
                            class="h-full w-full object-cover">
                        <!-- Placeholder si aucune couverture n'est disponible -->
                        <span class="text-6xl" aria-hidden="true">📖</span>

                        <!-- Étiquette de statut -->
                        <div class="absolute top-2 right-2">
                            <!-- Badge vert pour les livres disponibles -->
                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold shadow">
                                ✓ Disponible
                            </span>
                            <!-- Badge rouge pour les livres empruntés -->
                            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold shadow">
                                ✗ En prêt
                            </span>
                        </div>
                    </div>

                    <!-- Informations du livre -->
                    <div class="p-4">
                        <!-- Titre du livre -->
                        <h2 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">
                            Le Père Goriot
                        </h2>

                        <!-- Auteur du livre -->
                        <p class="text-gray-600 mb-2">
                            <span class="font-semibold">Auteur :</span>
                            Balzac
                        </p>

                        <!-- ID du livre -->
                        <p class="text-gray-500 text-sm">
                            Référence : #1414
                        </p>
                    </div>
                </article>
            </div>
        </section>
    </main>
    <!-- Pied de page -->
    <footer class="bg-gray-800 text-white mt-auto" role="contentinfo">
        <div class="container mx-auto px-4 py-6">
            <div class="text-center">
                <!-- Informations de copyright -->
                <p class="text-gray-300">
                    &copy; 2023 Bibliothèque. Tous droits réservés.
                </p>
                <!-- Informations supplémentaires -->
                <p class="text-gray-400 text-sm mt-2">
                    Système de gestion de bibliothèque
                </p>
            </div>
        </div>
    </footer>
</body>

</html>