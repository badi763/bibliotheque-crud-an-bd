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
        <!-- En-tête de la page -->
        <header class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">
                    Gestion des livres
                </h1>
                <p class="text-gray-600">
                    Total : 561 livre(s)
                </p>
            </div>

            <!-- Bouton pour ajouter un nouveau livre -->
            <a href="livre_add.php" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition shadow">
                ➕ Ajouter un livre
            </a>
        </header>

        <!-- Section de la liste des livres -->
        <section aria-label="Liste des livres">

            <!-- Message si aucun livre n'est disponible -->
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p class="font-bold">Aucun livre</p>
                <p>La bibliothèque ne contient actuellement aucun livre.</p>
            </div>

            <!-- Tableau des livres -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Titre
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Auteur
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Emprunté par
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">

                        <tr class="hover:bg-gray-50">
                            <!-- ID du livre -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                12
                            </td>

                            <!-- Titre du livre -->
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                Joli titre
                            </td>

                            <!-- Auteur du livre -->
                            <td class="px-6 py-4 text-sm text-gray-500">
                                Auteur lambda
                            </td>

                            <!-- Statut du livre -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm">

                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    ✓ Disponible
                                </span>

                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    ✗ En prêt
                                </span>

                            </td>

                            <!-- Abonné ayant emprunté le livre -->
                            <td class="px-6 py-4 text-sm text-gray-500">
                                Marcel La brute

                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <!-- Bouton modifier -->
                                <a href="livre_edit.php"
                                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded transition font-medium">
                                    ✏️ Modifier
                                </a>

                                <!-- Bouton supprimer -->
                                <a href="livre_delete.php"
                                    class="inline-block bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition font-medium"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?');">
                                    🗑️ Supprimer
                                </a>
                            </td>
                        </tr>

                    </tbody>
                </table>
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