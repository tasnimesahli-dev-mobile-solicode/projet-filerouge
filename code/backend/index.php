<?php
// backend/index.php - Page d'accueil de l'espace Admin Helpdesk
require_once __DIR__ . '/config.php';

// Statistiques rapides sur les tickets
$stat_total = 0;
$stat_ouverts = 0;
$stat_en_cours = 0;
$stat_resolus = 0;

$res_stats = mysqli_query($conn, "
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN statut = 'Ouvert' THEN 1 ELSE 0 END) AS ouverts,
        SUM(CASE WHEN statut = 'En cours' THEN 1 ELSE 0 END) AS en_cours,
        SUM(CASE WHEN statut = 'Résolu' THEN 1 ELSE 0 END) AS resolus
    FROM ticket
");
if ($res_stats && $row_stats = mysqli_fetch_assoc($res_stats)) {
    $stat_total = (int)$row_stats['total'];
    $stat_ouverts = (int)$row_stats['ouverts'];
    $stat_en_cours = (int)$row_stats['en_cours'];
    $stat_resolus = (int)$row_stats['resolus'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Helpdesk</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-hero {
            text-align: center;
            padding: 30px 10px;
            max-width: 750px;
            margin: 0 auto 30px auto;
        }
        .dashboard-hero h1 {
            font-size: 2rem;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .dashboard-hero p {
            color: #64748b;
            font-size: 1.05rem;
        }
        .dashboard-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <span>🎫 Helpdesk</span>
            <span class="logo-badge">Admin</span>
        </div>
        <nav>
            <a href="tickets.php">Tickets</a>
            <a href="ajouter_ticket.php" class="btn btn-ajouter btn-sm">+ Nouveau Ticket</a>
        </nav>
    </header>

    <main class="container">
        <div class="dashboard-hero">
            <h1>Plateforme d'Administration Helpdesk</h1>
            <p>
                Bienvenue dans l'espace d'administration. Vous pouvez gérer l'intégralité du cycle de vie des tickets d'assistance : création, consultation, mise à jour et suppression avec suppression en cascade des commentaires.
            </p>
            <div class="dashboard-actions">
                <a href="tickets.php" class="btn btn-modifier" style="padding: 12px 24px; font-size: 1rem;">
                    📋 Voir tous les tickets
                </a>
                <a href="ajouter_ticket.php" class="btn btn-ajouter" style="padding: 12px 24px; font-size: 1rem;">
                    ➕ Ajouter un ticket
                </a>
            </div>
        </div>

        <!-- Cartes statistiques récapitulatives -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-card-title">Total des Tickets</span>
                <span class="stat-card-value"><?= $stat_total ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-card-title">Tickets Ouverts</span>
                <span class="stat-card-value" style="color: #2563eb;"><?= $stat_ouverts ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-card-title">En Cours de Traitement</span>
                <span class="stat-card-value" style="color: #d97706;"><?= $stat_en_cours ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-card-title">Tickets Résolus</span>
                <span class="stat-card-value" style="color: #16a34a;"><?= $stat_resolus ?></span>
            </div>
        </div>
    </main>

</body>
</html>
