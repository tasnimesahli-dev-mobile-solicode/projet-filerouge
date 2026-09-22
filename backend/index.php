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
        <div class="logo">Helpdesk</div>
        <nav>
            <a href="index.php" class="active">Accueil</a>
            <a href="utilisateurs.php">Gestion des utilisateurs</a>
        </nav>
    </header>

    <main class="container home-container">
        <!-- Titre principal et courte description -->
        <section class="hero-section">
            <h1 class="main-title">Gestion des tickets et des problèmes utilisateurs</h1>
            <p class="subtitle">
                Le système <strong>Helpdesk</strong> est une plateforme simple et efficace permettant d'enregistrer, d'organiser et de suivre la résolution des incidents techniques signalés par les utilisateurs de l'entreprise.
            </p>
        </section>

        <!-- Section explicative du principe -->
        <section class="workflow-section">
            <h2 class="section-title">Principe de fonctionnement</h2>
            
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-badge">1</div>
                    <h3>Signaler un problème</h3>
                    <p>L'utilisateur soumet un ticket décrivant la difficulté ou la panne rencontrée.</p>
                </div>

                <div class="step-arrow">&rarr;</div>

                <div class="step-card">
                    <div class="step-badge">2</div>
                    <h3>Traitement par le Support</h3>
                    <p>L'équipe support prend en charge la demande, échange avec l'utilisateur et intervient.</p>
                </div>

                <div class="step-arrow">&rarr;</div>

                <div class="step-card">
                    <div class="step-badge">3</div>
                    <h3>Résolution</h3>
                    <p>Le problème est réglé, le ticket est mis à jour puis clôturé avec confirmation.</p>
                </div>
            </div>
        </section>

        <!-- Bouton principal d'action -->
        <section class="cta-section">
            <a href="utilisateurs.php" class="btn btn-cta">Gestion des utilisateurs</a>
        </section>
    </main>

</body>
</html>
