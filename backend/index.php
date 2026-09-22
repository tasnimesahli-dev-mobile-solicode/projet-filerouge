<?php
// index.php - Page d'accueil : Gestion des tickets et des problèmes utilisateurs
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des tickets et des problèmes utilisateurs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="logo">Helpdesk - Administration</div>
        <nav>
            <a href="index.php" class="active">Accueil</a>
            <a href="utilisateurs.php">Utilisateurs</a>
            <a href="categories.php">Catégories</a>
            <a href="tickets.php">Tickets</a>
            <a href="commentaires.php">Commentaires</a>
        </nav>
    </header>

    <main class="container home-container">
        <!-- Titre principal et courte description -->
        <section class="hero-section">
            <h1 class="main-title">Gestion des tickets et des problèmes utilisateurs</h1>
            <p class="subtitle">
                Plateforme d'administration centralisée du système <strong>Helpdesk</strong> permettant de superviser les utilisateurs, d'organiser les catégories d'incidents, de suivre les tickets d'assistance et de modérer les échanges.
            </p>
        </section>

        <!-- Grille des modules d'administration -->
        <section class="workflow-section">
            <h2 class="section-title">Espace d'Administration</h2>
            
            <div class="admin-modules-grid">
                <div class="step-card">
                    <div class="module-icon">👥</div>
                    <h3>Utilisateurs</h3>
                    <p>Gestion complète des comptes (afficher, ajouter, modifier, supprimer).</p>
                    <a href="utilisateurs.php" class="btn btn-modifier" style="margin-top: 15px; font-size: 0.85rem;">Gérer les utilisateurs</a>
                </div>

                <div class="step-card">
                    <div class="module-icon">📁</div>
                    <h3>Catégories</h3>
                    <p>Organisation des catégories d'incidents (afficher, ajouter, modifier, supprimer).</p>
                    <a href="categories.php" class="btn btn-modifier" style="margin-top: 15px; font-size: 0.85rem;">Gérer les catégories</a>
                </div>

                <div class="step-card">
                    <div class="module-icon">🎫</div>
                    <h3>Tickets</h3>
                    <p>Supervision des signalements d'incidents (afficher et supprimer les tickets).</p>
                    <a href="tickets.php" class="btn btn-modifier" style="margin-top: 15px; font-size: 0.85rem;">Consulter les tickets</a>
                </div>

                <div class="step-card">
                    <div class="module-icon">💬</div>
                    <h3>Commentaires</h3>
                    <p>Consultation et modération des messages échangés (afficher et supprimer).</p>
                    <a href="commentaires.php" class="btn btn-modifier" style="margin-top: 15px; font-size: 0.85rem;">Gérer les commentaires</a>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <a href="utilisateurs.php" class="btn btn-cta">Accéder à l'Administration</a>
        </section>
    </main>

</body>
</html>
