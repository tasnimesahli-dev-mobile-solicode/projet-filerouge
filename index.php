<?php
// index.php - Page d'accueil Helpdesk
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpdesk - Accueil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>Helpdesk</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="utilisateurs.php">Gestion des Utilisateurs</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h2>Bienvenue sur l'application Helpdesk</h2>
        </div>

        <div class="welcome-card">
            <p>
                <strong>Helpdesk</strong> est une solution web dédiée au suivi et à la gestion centralisée des tickets d'assistance au sein de l'entreprise.
            </p>
            <p>
                Elle permet aux <strong>Clients</strong> de signaler leurs incidents techniques, aux agents du <strong>Support</strong> de prendre en charge et résoudre les tickets, et aux <strong>Administrateurs</strong> de superviser l'ensemble de la plateforme.
            </p>

            <ul class="roles-list">
                <li><strong>Client :</strong> Création et suivi de l'avancement de ses tickets d'incident.</li>
                <li><strong>Support :</strong> Traitement des tickets, changement de statut et réponses aux utilisateurs.</li>
                <li><strong>Admin :</strong> Administration complète des utilisateurs, tickets, catégories et commentaires.</li>
            </ul>

            <div style="margin-top: 30px;">
                <a href="utilisateurs.php" class="btn btn-modifier">Accéder à la gestion des utilisateurs &rarr;</a>
            </div>
        </div>
    </div>

</body>
</html>
