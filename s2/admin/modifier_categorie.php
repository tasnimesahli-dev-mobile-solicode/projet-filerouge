<?php
// fileRouge-s2/admin/modifier_categorie.php - Modifier une Catégorie (Admin avec Tailwind CSS & Fetch API)
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../repositories/CategorieRepository.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: categories.php");
    exit;
}

$pdo = Database::getInstance();
$repo = new CategorieRepository($pdo);
$categorie = $repo->findById($id);

if (!$categorie) {
    header("Location: categories.php");
    exit;
}

$nbTickets = $repo->countTickets($id);
?>
<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Modifier la Catégorie #<?= $id ?> (V2)</title>
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

    <!-- En-tête / Barre de navigation -->
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
                    <a href="categories.php" class="px-3 py-2 rounded-md text-sm font-semibold text-slate-600 hover:text-brand-600 transition">
                        &larr; Retour à la liste des catégories
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Zone principale -->
    <main class="flex-1 max-w-2xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- Fil d'Ariane -->
        <nav class="flex mb-6 text-sm text-slate-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li><a href="categories.php" class="hover:text-brand-600">Catégories</a></li>
                <li><span>/</span></li>
                <li class="font-medium text-slate-900">Modifier la catégorie #<?= $id ?></li>
            </ol>
        </nav>

        <!-- Message d'alerte dynamique (Fetch API) -->
        <div id="alertBox" class="hidden mb-6 rounded-xl p-4 transition-all duration-300">
            <div class="flex items-center">
                <span id="alertIcon" class="text-xl mr-3"></span>
                <div id="alertText" class="text-sm font-medium"></div>
            </div>
        </div>

        <!-- Carte Formulaire Tailwind CSS -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-xl">
                        ✏️
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">Modifier la Catégorie #<?= $id ?></h1>
                        <p class="text-xs text-slate-500">Mise à jour asynchrone via la méthode HTTP PUT et Fetch API.</p>
                    </div>
                </div>
                <div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                        <?= $nbTickets ?> ticket(s) lié(s)
                    </span>
                </div>
            </div>

            <!-- Formulaire géré par Fetch API / AJAX -->
            <form id="editCategoryForm" class="space-y-6">
                <input type="hidden" id="category_id" value="<?= $id ?>">

                <div>
                    <label for="nom_categorie" class="block text-sm font-semibold text-slate-700 mb-1">
                        Nom de la catégorie <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="nom_categorie" 
                           name="nom_categorie" 
                           value="<?= htmlspecialchars($categorie->getNomCategorie()) ?>" 
                           required 
                           maxlength="100"
                           class="block w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition">
                    <p class="mt-1.5 text-xs text-slate-400">Le nom doit être unique et comporter entre 2 et 100 caractères.</p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                    <a href="categories.php" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                        Annuler
                    </a>
                    <button type="submit" 
                            id="submitBtn" 
                            class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition">
                        <span id="btnSpinner" class="hidden mr-2 animate-spin">⏳</span>
                        <span>Enregistrer les modifications</span>
                    </button>
                </div>
            </form>
        </div>

    </main>

    <!-- Gestion asynchrone Fetch API / AJAX -->
    <script>
        const form = document.getElementById('editCategoryForm');
        const categoryId = document.getElementById('category_id').value;
        const submitBtn = document.getElementById('submitBtn');
        const btnSpinner = document.getElementById('btnSpinner');
        const alertBox = document.getElementById('alertBox');
        const alertIcon = document.getElementById('alertIcon');
        const alertText = document.getElementById('alertText');

        function showAlert(message, type = 'error') {
            alertBox.classList.remove('hidden', 'bg-rose-50', 'text-rose-800', 'border-rose-200', 'bg-emerald-50', 'text-emerald-800', 'border-emerald-200');
            alertBox.classList.add('border');

            if (type === 'success') {
                alertBox.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-200');
                alertIcon.textContent = '✅';
            } else {
                alertBox.classList.add('bg-rose-50', 'text-rose-800', 'border-rose-200');
                alertIcon.textContent = '⚠️';
            }

            alertText.textContent = message;
            alertBox.classList.remove('hidden');
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const nom = document.getElementById('nom_categorie').value.trim();

            if (!nom) {
                showAlert('Veuillez renseigner un nom de catégorie.');
                return;
            }

            submitBtn.disabled = true;
            btnSpinner.classList.remove('hidden');

            try {
                // Requête HTTP PUT asynchrone avec Fetch API et JSON
                const response = await fetch(`../api/categories.php?id=${categoryId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ nom_categorie: nom })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    showAlert(result.message || 'Catégorie modifiée avec succès ! Redirection...', 'success');
                    setTimeout(() => {
                        window.location.href = 'categories.php';
                    }, 1000);
                } else {
                    showAlert(result.message || 'Une erreur est survenue lors de la mise à jour.', 'error');
                }
            } catch (err) {
                showAlert('Erreur réseau : impossible de joindre le serveur API.', 'error');
            } finally {
                submitBtn.disabled = false;
                btnSpinner.classList.add('hidden');
            }
        });
    </script>

</body>
</html>
