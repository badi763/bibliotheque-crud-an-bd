<!DOCTYPE html>
<html lang="fr">
<?php
echo password_hash('admin', PASSWORD_DEFAULT);
?>

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
        <!-- Titre de la page -->
        <header class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                Tableau de bord administrateur
            </h1>
            <p class="text-gray-600">
                Gestion des abonnés et suivi des emprunts
            </p>
        </header>

        <!-- Section des statistiques -->
        <section class="mb-8" aria-label="Statistiques">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Nombre total d'abonnés -->
                <article class="bg-blue-100 rounded-lg p-6 shadow">
                    <h2 class="text-lg font-semibold text-blue-800 mb-2">Total d'abonnés</h2>
                    <p class="text-4xl font-bold text-blue-600">45</p>
                </article>

                <!-- Nombre d'emprunts en cours -->
                <article class="bg-orange-100 rounded-lg p-6 shadow">
                    <h2 class="text-lg font-semibold text-orange-800 mb-2">Emprunts en cours</h2>
                    <p class="text-4xl font-bold text-orange-600">14</p>
                </article>

                <!-- Nombre d'abonnés avec retards -->
                <article class="bg-red-100 rounded-lg p-6 shadow">
                    <h2 class="text-lg font-semibold text-red-800 mb-2">Abonnés avec retards</h2>
                    <p class="text-4xl font-bold text-red-600">4</p>
                </article>
            </div>
        </section>

        <!-- Section de la liste des abonnés -->
        <section aria-label="Liste des abonnés">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                Liste des abonnés
            </h2>

            <!-- Message si aucun abonné n'existe -->
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p class="font-bold">Aucun abonné</p>
                <p>La bibliothèque n'a actuellement aucun abonné enregistré.</p>
            </div>

            <!-- Tableau des abonnés -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Civilité
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prénom
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total emprunts
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                En cours
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">

                        <!-- Ligne de l'abonné (en rouge si emprunts non rendus) -->
                        <tr class="">
                            <!-- ID de l'abonné -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                4
                            </td>

                            <!-- Civilité -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Mme
                            </td>

                            <!-- Nom -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Croche
                            </td>

                            <!-- Prénom -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Sarah
                            </td>

                            <!-- Email -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <a href="mailto:" class="text-blue-600 hover:text-blue-800">
                                    sarahcroche@email.fr
                                </a>
                            </td>

                            <!-- Nombre total d'emprunts -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    14
                                </span>
                            </td>

                            <!-- Nombre d'emprunts en cours -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">

                                <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                    ⚠ 2non rendu(s)
                                </span>

                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    ✓ Aucun
                                </span>

                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="" class="text-blue-600 hover:text-blue-800 font-medium">
                                    Voir historique
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mt-8 bg-blue-50 rounded-lg p-6" aria-label="Historique des emprunts">
            <header class="mb-4 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        Historique des emprunts
                    </h2>
                    <p class="text-gray-600 mt-1">
                        Mme Sarah Croche (sarahcroche@email)
                    </p>
                </div>
                <a href="?" class="text-blue-600 hover:text-blue-800 font-medium">
                    ✕ Fermer
                </a>
            </header>

            <!-- Message si aucun emprunt -->
            <p class="text-gray-600">Cet abonné n'a effectué aucun emprunt.</p>
            <!-- Tableau de l'historique -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID Emprunt
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Livre
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date de sortie
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date de retour
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Durée
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50">
                            <!-- ID de l'emprunt -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #515
                            </td>

                            <!-- Informations du livre -->
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">Toto titre</div>
                                <div class="text-gray-500 text-xs">Toto auteur</div>
                            </td>

                            <!-- Date de sortie -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                12/12/2023
                            </td>

                            <!-- Date de retour -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                02/12/2024
                            </td>

                            <!-- Durée de l'emprunt -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                21 jour(s)
                            </td>

                            <!-- Statut -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    ✓ Rendu
                                </span>
                                <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                    ⚠ En cours
                                </span>
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