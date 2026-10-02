<?php
// fileRouge-s2/admin/categories.php - Gestion des Catégories (Admin CRUD avec Tailwind CSS & Fetch API)
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../repositories/CategorieRepository.php';

// Récupération initiale côté serveur en POO
$pdo = Database::getInstance();
$repo = new CategorieRepository($pdo);
$categories = $repo->findAllWithTicketCount();

$totalCategories = count($categories);
$totalTicketsAssocies = array_sum(array_column($categories, 'nb_tickets'));
?>
<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Gestion des Catégories (V2)</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex flex-col text-slate-800 antialiased">

    <!-- En-tête / Barre de navigation avec Tailwind CSS -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">📁</span>
                    <div>
                        <span class="text-lg font-bold text-slate-900 tracking-tight">Helpdesk</span>
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-800">
                            Version 2 (POO & API)
                        </span>
                    </div>
                </div>
                <nav class="flex items-center space-x-4">
                    <a href="categories.php" class="px-3 py-2 rounded-md text-sm font-semibold text-brand-600 bg-brand-50 border border-brand-100">
                        Catégories
                    </a>
                    <a href="ajouter_categorie.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 transition">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nouvelle Catégorie
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Zone principale -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Notification Toast Dynamique (Fetch API / AJAX) -->
        <div id="toastNotification" class="hidden mb-6 rounded-lg p-4 transition-all duration-300 transform shadow-md flex items-center justify-between">
            <div class="flex items-center">
                <span id="toastIcon" class="text-xl mr-3"></span>
                <span id="toastMessage" class="text-sm font-medium"></span>
            </div>
            <button onclick="hideToast()" class="text-slate-400 hover:text-slate-600 text-lg ml-4">&times;</button>
        </div>

        <!-- En-tête de section -->
        <div class="md:flex md:items-center md:justify-between mb-8 pb-4 border-b border-slate-200">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Gestion des Catégories
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Interface d'administration pour la gestion des catégories d'incidents via l'API RESTful et Fetch API.
                </p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <a href="ajouter_categorie.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 transition">
                    + Ajouter une Catégorie
                </a>
            </div>
        </div>

        <!-- Cartes Statistiques KPI -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center">
                <div class="p-3 rounded-lg bg-indigo-50 text-indigo-600 text-2xl mr-4">📂</div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Catégories</p>
                    <p id="statTotal" class="text-2xl font-bold text-slate-900"><?= $totalCategories ?></p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center">
                <div class="p-3 rounded-lg bg-emerald-50 text-emerald-600 text-2xl mr-4">🏷️</div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tickets Classés</p>
                    <p id="statTickets" class="text-2xl font-bold text-slate-900"><?= $totalTicketsAssocies ?></p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center">
                <div class="p-3 rounded-lg bg-sky-50 text-sky-600 text-2xl mr-4">⚡</div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Mode Fonctionnement</p>
                    <p class="text-sm font-bold text-sky-700">API REST JSON & Fetch</p>
                </div>
            </div>
        </div>

        <!-- Carte Tableau -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <!-- Barre d'outils du tableau avec recherche dynamique en temps réel -->
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50">
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           id="searchInput" 
                           oninput="filterCategories()"
                           placeholder="Rechercher une catégorie en direct..." 
                           class="block w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div class="text-xs text-slate-500 font-medium">
                    <span id="filteredCount"><?= $totalCategories ?></span> catégorie(s) affichée(s)
                </div>
            </div>

            <!-- Tableau des catégories -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider w-20">ID</th>
                            <th scope="col" class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Nom de la Catégorie</th>
                            <th scope="col" class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Tickets Rattachés</th>
                            <th scope="col" class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider text-right w-48">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTableBody" class="divide-y divide-slate-200 bg-white">
                        <?php if (empty($categories)): ?>
                            <tr id="emptyRow">
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <span class="text-3xl block mb-2">📭</span>
                                    Aucune catégorie trouvée.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr id="row-<?= (int)$cat['id_categorie'] ?>" class="hover:bg-slate-50/80 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-400">
                                        #<?= (int)$cat['id_categorie'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="h-8 w-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm mr-3">
                                                📁
                                            </span>
                                            <span class="text-sm font-semibold text-slate-900 category-name">
                                                <?= htmlspecialchars($cat['nom_categorie']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <?php if ((int)$cat['nb_tickets'] > 0): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?= (int)$cat['nb_tickets'] ?> ticket(s)
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                                0 ticket
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <!-- Modifier -->
                                        <a href="modifier_categorie.php?id=<?= (int)$cat['id_categorie'] ?>" 
                                           class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 transition">
                                            Modifier
                                        </a>

                                        <!-- Supprimer (Fetch API / AJAX) -->
                                        <button onclick="confirmDelete(<?= (int)$cat['id_categorie'] ?>, '<?= htmlspecialchars(addslashes($cat['nom_categorie'])) ?>', <?= (int)$cat['nb_tickets'] ?>)" 
                                                class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 transition">
                                            Supprimer
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal de confirmation de suppression Tailwind -->
    <div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 transform transition-all">
            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 text-rose-600 mx-auto mb-4">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 text-center mb-2">Confirmer la suppression</h3>
            <p id="deleteModalText" class="text-sm text-slate-500 text-center mb-6"></p>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                    Annuler
                </button>
                <button type="button" id="confirmDeleteBtn" class="px-4 py-2 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition inline-flex items-center">
                    <span id="deleteBtnSpinner" class="hidden mr-2 animate-spin">⏳</span>
                    Supprimer définitivement
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts JavaScript interactifs : Fetch API & AJAX -->
    <script>
        let currentDeleteId = null;

        // Afficher une notification Toast
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const icon = document.getElementById('toastIcon');
            const text = document.getElementById('toastMessage');

            toast.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-800', 'border-emerald-200', 'bg-rose-50', 'text-rose-800', 'border-rose-200');
            toast.classList.add('border');

            if (type === 'success') {
                toast.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-200');
                icon.textContent = '✅';
            } else {
                toast.classList.add('bg-rose-50', 'text-rose-800', 'border-rose-200');
                icon.textContent = '⚠️';
            }

            text.textContent = message;
            toast.classList.remove('hidden');

            setTimeout(() => {
                hideToast();
            }, 5000);
        }

        function hideToast() {
            document.getElementById('toastNotification').classList.add('hidden');
        }

        // Filtrage temps réel par recherche
        function filterCategories() {
            const term = document.getElementById('searchInput').value.toLowerCase().trim();
            const rows = document.querySelectorAll('#categoriesTableBody tr[id^="row-"]');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.querySelector('.category-name').textContent.toLowerCase();
                if (name.includes(term)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('filteredCount').textContent = visibleCount;
        }

        // Recharger les catégories en direct via Fetch API / AJAX
        async function loadCategories(silent = true) {
            try {
                const response = await fetch('../api/categories.php');
                const result = await response.json();

                if (result.success) {
                    renderTable(result.data);
                    document.getElementById('statTotal').textContent = result.count;
                    const totalTickets = result.data.reduce((acc, c) => acc + parseInt(c.nb_tickets || 0), 0);
                    document.getElementById('statTickets').textContent = totalTickets;
                    if (!silent) {
                        showToast('Données actualisées avec succès.');
                    }
                } else {
                    if (!silent) {
                        showToast(result.message || 'Erreur lors du chargement.', 'error');
                    }
                }
            } catch (err) {
                if (!silent) {
                    showToast('Erreur de connexion à l\'API.', 'error');
                }
            }
        }

        // Rendu dynamique du tableau avec les données JSON de l'API
        function renderTable(categories) {
            const tbody = document.getElementById('categoriesTableBody');
            tbody.innerHTML = '';

            if (categories.length === 0) {
                tbody.innerHTML = `
                    <tr id="emptyRow">
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                            <span class="text-3xl block mb-2">📭</span>
                            Aucune catégorie enregistrée.
                        </td>
                    </tr>
                `;
                document.getElementById('filteredCount').textContent = 0;
                return;
            }

            categories.forEach(cat => {
                const safeName = cat.nom_categorie.replace(/"/g, '&quot;').replace(/'/g, "\\'");
                const row = document.createElement('tr');
                row.id = `row-${cat.id_categorie}`;
                row.className = 'hover:bg-slate-50/80 transition duration-150';
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-400">
                        #${cat.id_categorie}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <span class="h-8 w-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm mr-3">
                                📁
                            </span>
                            <span class="text-sm font-semibold text-slate-900 category-name">
                                ${escapeHtml(cat.nom_categorie)}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        ${cat.nb_tickets > 0 
                            ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${cat.nb_tickets} ticket(s)</span>`
                            : `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">0 ticket</span>`
                        }
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <a href="modifier_categorie.php?id=${cat.id_categorie}" 
                           class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 transition">
                            Modifier
                        </a>
                        <button onclick="confirmDelete(${cat.id_categorie}, '${safeName}', ${cat.nb_tickets})" 
                                class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 transition">
                            Supprimer
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            document.getElementById('filteredCount').textContent = categories.length;
        }

        // Ouverture de la modale de suppression
        function confirmDelete(id, name, ticketCount) {
            currentDeleteId = id;
            const modal = document.getElementById('deleteModal');
            const text = document.getElementById('deleteModalText');

            if (ticketCount > 0) {
                text.innerHTML = `Êtes-vous sûr de vouloir supprimer la catégorie <strong>« ${escapeHtml(name)} »</strong> ?<br><span class="text-amber-600 font-semibold mt-2 inline-block">⚠️ Attention : ${ticketCount} ticket(s) y sont rattachés. L'API refusera la suppression pour protéger les données.</span>`;
            } else {
                text.innerHTML = `Êtes-vous sûr de vouloir supprimer définitivement la catégorie <strong>« ${escapeHtml(name)} »</strong> ? Cette action est irréversible.`;
            }

            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            currentDeleteId = null;
        }

        // Exécution de la suppression avec Fetch API / AJAX
        document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
            if (!currentDeleteId) return;

            const btn = document.getElementById('confirmDeleteBtn');
            const spinner = document.getElementById('deleteBtnSpinner');
            btn.disabled = true;
            spinner.classList.remove('hidden');

            try {
                // Requête HTTP DELETE asynchrone vers l'API PHP
                const response = await fetch(`../api/categories.php?id=${currentDeleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    closeDeleteModal();
                    await loadCategories(true);
                    showToast(result.message || 'Catégorie supprimée avec succès.');
                } else {
                    closeDeleteModal();
                    showToast(result.message || 'Erreur lors de la suppression.', 'error');
                }
            } catch (err) {
                closeDeleteModal();
                showToast('Erreur réseau lors de la communication avec l\'API.', 'error');
            } finally {
                btn.disabled = false;
                spinner.classList.add('hidden');
            }
        });

        function escapeHtml(string) {
            return String(string).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>

</body>
</html>
