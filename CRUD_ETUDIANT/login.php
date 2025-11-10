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
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Erreur</p>
                    <p>Petite erreur</p>
                </div>

                <!-- Formulaire -->
                <form method="POST" action="" novalidate>
                    <!-- Champ Login -->
                    <div class="mb-6">
                        <label for="login" class="block text-gray-700 font-semibold mb-2">
                            Identifiant
                        </label>
                        <input
                            type="text"
                            id="login"
                            name="login"
                            required
                            autocomplete="username"
                            value=""
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Entrez votre identifiant">
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