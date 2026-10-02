<?php
// backend/tickets.php - Page Admin : Affichage et suppression des Tickets
require_once __DIR__ . '/config.php';

$message_erreur = '';
$message_succes = '';

// ==========================================
// 1. SUPPRIMER UN TICKET (Delete)
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id_ticket_a_supprimer = (int)$_GET['id'];

    if ($id_ticket_a_supprimer > 0) {
        // Préparation de la requête de suppression avec MySQLi
        // Grâce à la contrainte ON DELETE CASCADE, les commentaires associés sont automatiquement supprimés
        $stmt_delete = mysqli_prepare($conn, "DELETE FROM ticket WHERE id_ticket = ?");
        if ($stmt_delete) {
            mysqli_stmt_bind_param($stmt_delete, "i", $id_ticket_a_supprimer);
            if (mysqli_stmt_execute($stmt_delete)) {
                mysqli_stmt_close($stmt_delete);
                header("Location: tickets.php?success=deleted");
                exit;
            } else {
                $message_erreur = "Erreur lors de la suppression du ticket : " . mysqli_error($conn);
                mysqli_stmt_close($stmt_delete);
            }
        } else {
            $message_erreur = "Erreur de préparation de la requête : " . mysqli_error($conn);
        }
    } else {
        $message_erreur = "Identifiant de ticket invalide.";
    }
}

// ==========================================
// 2. MESSAGES DE NOTIFICATION
// ==========================================
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'added') {
        $message_succes = "Le ticket a été ajouté avec succès.";
    } elseif ($_GET['success'] === 'updated') {
        $message_succes = "Le ticket a été modifié avec succès.";
    } elseif ($_GET['success'] === 'deleted') {
        $message_succes = "Le ticket et ses commentaires associés ont été supprimés avec succès.";
    }
}

// ==========================================
// 3. STATISTIQUES DES TICKETS
// ==========================================
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

// ==========================================
// 4. RÉCUPÉRATION DES TICKETS (Read)
// ==========================================
$sql = "
    SELECT 
        t.id_ticket,
        t.titre,
        t.description,
        t.date_creation,
        t.statut,
        u.id_utilisateur,
        u.nom,
        u.prenom,
        u.role,
        c.id_categorie,
        c.nom_categorie,
        (SELECT COUNT(*) FROM commentaire comm WHERE comm.id_ticket = t.id_ticket) AS nb_commentaires
    FROM ticket t
    LEFT JOIN utilisateur u ON t.id_utilisateur = u.id_utilisateur
    LEFT JOIN categorie c ON t.id_categorie = c.id_categorie
    ORDER BY t.date_creation DESC
";

$result_tickets = mysqli_query($conn, $sql);
$tickets = [];
if ($result_tickets) {
    while ($row = mysqli_fetch_assoc($result_tickets)) {
        $tickets[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Gestion des Tickets</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="logo">
            <span>🎫 Helpdesk</span>
            <span class="logo-badge">Admin</span>
        </div>
        <nav>
            <a href="tickets.php" class="active">Tickets</a>
            <a href="ajouter_ticket.php" class="btn btn-ajouter btn-sm">+ Nouveau Ticket</a>
        </nav>
    </header>

    <main class="container">
        <!-- En-tête de section -->
        <div class="page-header">
            <div>
                <h2>Gestion des Tickets</h2>
                <p class="page-header-subtitle">
                    Espace d'administration pour créer, consulter, mettre à jour et supprimer les tickets d'assistance.
                </p>
            </div>
            <div>
                <a href="ajouter_ticket.php" class="btn btn-ajouter">
                    <span>+</span> Ajouter un ticket
                </a>
            </div>
        </div>

        <!-- Messages de notification -->
        <?php if (!empty($message_succes)): ?>
            <div class="alert alert-success">
                <strong>Succès :</strong> <?= htmlspecialchars($message_succes) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($message_erreur)): ?>
            <div class="alert alert-danger">
                <strong>Erreur :</strong> <?= htmlspecialchars($message_erreur) ?>
            </div>
        <?php endif; ?>

        <!-- Cartes statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-card-title">Total Tickets</span>
                <span class="stat-card-value"><?= $stat_total ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-card-title">Ouverts</span>
                <span class="stat-card-value" style="color: #2563eb;"><?= $stat_ouverts ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-card-title">En cours</span>
                <span class="stat-card-value" style="color: #d97706;"><?= $stat_en_cours ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-card-title">Résolus</span>
                <span class="stat-card-value" style="color: #16a34a;"><?= $stat_resolus ?></span>
            </div>
        </div>

        <!-- Tableau des tickets (Read) -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Titre & Description</th>
                        <th>Catégorie</th>
                        <th>Auteur / Demandeur</th>
                        <th>Date de création</th>
                        <th>Statut</th>
                        <th style="text-align: center;">Commentaires</th>
                        <th style="width: 170px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-state-icon">📭</div>
                                    <h3>Aucun ticket enregistré</h3>
                                    <p>Il n'y a actuellement aucun ticket dans la base de données.</p>
                                    <a href="ajouter_ticket.php" class="btn btn-ajouter">+ Créer le premier ticket</a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $t): ?>
                            <?php
                                // Détermination de la classe CSS du statut
                                $statusClass = 'badge-ouvert';
                                if ($t['statut'] === 'En cours') {
                                    $statusClass = 'badge-en-cours';
                                } elseif ($t['statut'] === 'Résolu') {
                                    $statusClass = 'badge-resolu';
                                } elseif ($t['statut'] === 'Fermé') {
                                    $statusClass = 'badge-ferme';
                                }

                                // Formatage de la date
                                $date_formatee = !empty($t['date_creation']) ? date('d/m/Y H:i', strtotime($t['date_creation'])) : 'N/A';

                                // Détermination du rôle
                                $roleClass = 'role-client';
                                if ($t['role'] === 'Admin') {
                                    $roleClass = 'role-admin';
                                } elseif ($t['role'] === 'Support') {
                                    $roleClass = 'role-support';
                                }
                            ?>
                            <tr>
                                <td>
                                    <strong>#<?= (int)$t['id_ticket'] ?></strong>
                                </td>
                                <td>
                                    <div class="ticket-title-link">
                                        <?= htmlspecialchars($t['titre']) ?>
                                    </div>
                                    <div class="ticket-desc-snippet" title="<?= htmlspecialchars($t['description']) ?>">
                                        <?= htmlspecialchars($t['description']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-cat">
                                        📁 <?= htmlspecialchars($t['nom_categorie'] ?? 'Sans catégorie') ?>
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <?= htmlspecialchars(trim(($t['prenom'] ?? '') . ' ' . ($t['nom'] ?? 'Utilisateur inconnu'))) ?>
                                        <?php if (!empty($t['role'])): ?>
                                            <span class="badge-role <?= $roleClass ?>"><?= htmlspecialchars($t['role']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 0.85rem; color: #475569;">
                                        <?= $date_formatee ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-statut <?= $statusClass ?>">
                                        <?= htmlspecialchars($t['statut']) ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 600;">
                                        💬 <?= (int)$t['nb_commentaires'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <!-- Modifier un ticket (Update) -->
                                        <a href="modifier_ticket.php?id=<?= (int)$t['id_ticket'] ?>" class="btn btn-modifier btn-sm">
                                            Modifier
                                        </a>

                                        <!-- Supprimer un ticket (Delete) -->
                                        <a href="tickets.php?action=supprimer&id=<?= (int)$t['id_ticket'] ?>" 
                                           class="btn btn-supprimer btn-sm"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer le ticket #<?= (int)$t['id_ticket'] ?> ?\n\nGrâce à ON DELETE CASCADE, tous les commentaires associés seront automatiquement supprimés.');">
                                            Supprimer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
