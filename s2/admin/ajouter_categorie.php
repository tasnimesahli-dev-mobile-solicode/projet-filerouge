<?php
// fileRouge-s2/admin/ajouter_categorie.php - Ajouter une Catégorie (Admin avec Tailwind CSS & Fetch API)
?>
<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Ajouter une Catégorie (V2)</title>
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
                <li class="font-medium text-slate-900">Nouvelle catégorie</li>
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
            <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl">
                    ➕
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Ajouter une Catégorie</h1>
                    <p class="text-xs text-slate-500">Ce formulaire communique en temps réel avec l'API REST via Fetch API.</p>
                </div>
            </div>

            <!-- Formulaire géré par Fetch API / AJAX -->
            <form id="addCategoryForm" class="space-y-6">
                <div>
                    <label for="nom_categorie" class="block text-sm font-semibold text-slate-700 mb-1">
                        Nom de la catégorie <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="nom_categorie" 
                           name="nom_categorie" 
                           required 
                           maxlength="100"
                           placeholder="Ex : Réseau, Matériel, Logiciel, Téléphonie..." 
                           class="block w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm transition">
                    <p class="mt-1.5 text-xs text-slate-400">Le nom doit être unique et comporter entre 2 et 100 caractères.</p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                    <a href="categories.php" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                        Annuler
                    </a>
                    <button type="submit" 
                            id="submitBtn" 
                            class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition">
                        <span id="btnSpinner" class="hidden mr-2 animate-spin">⏳</span>
                        <span>Créer la catégorie</span>
                    </button>
                </div>
            </form>
        </div>

    </main>

    <!-- Gestion asynchrone Fetch API / AJAX -->
    <script>
        const form = document.getElementById('addCategoryForm');
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

            // Désactivation du bouton et activation du spinner
            submitBtn.disabled = true;
            btnSpinner.classList.remove('hidden');

            try {
                // Requête HTTP POST asynchrone avec Fetch API et JSON
                const response = await fetch('../api/categories.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ nom_categorie: nom })
                });

                const result = await response.json();

                if (response.status === 201 && result.success) {
                    showAlert(result.message || 'Catégorie créée avec succès ! Redirection en cours...', 'success');
                    form.reset();
                    // Redirection automatique après 1 seconde
                    setTimeout(() => {
                        window.location.href = 'categories.php';
                    }, 1000);
                } else {
                    showAlert(result.message || 'Une erreur est survenue lors de la création.', 'error');
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
