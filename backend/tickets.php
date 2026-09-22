<?php
// tickets.php - Consultation et suppression des tickets (Admin)
require_once __DIR__ . '/config.php';

$message_erreur = '';
$message_succes = '';

// Traitement de la suppression d'un ticket
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id_ticket_a_supprimer = (int)$_GET['id'];

    try {
        // Suppression préalable des commentaires liés pour respecter les contraintes de clés étrangères
        $stmtDelComments = $pdo->prepare("DELETE FROM commentaire WHERE id_ticket = ?");
        $stmtDelComments->execute([$id_ticket_a_supprimer]);

        // Suppression du ticket
        $stmtDelTicket = $pdo->prepare("DELETE FROM ticket WHERE id_ticket = ?");
        $stmtDelTicket->execute([$id_ticket_a_supprimer]);

        header("Location: tickets.php?success=deleted");
        exit;
    } catch (PDOException $e) {
        $message_erreur = "Erreur lors de la suppression du ticket : " . $e->getMessage();
    }
}

// Messages de confirmation
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'deleted') {
        $message_succes = "Ticket supprimé avec succès.";
    }
}

// Récupération de tous les tickets avec nom d'utilisateur, catégorie et décompte des commentaires
$query = "SELECT t.*, u.nom, u.prenom, c.nom_categorie,
          (SELECT COUNT(*) FROM commentaire comm WHERE comm.id_ticket = t.id_ticket) AS nb_commentaires
          FROM ticket t
          LEFT JOIN utilisateur u ON t.id_utilisateur = u.id_utilisateur
          LEFT JOIN categorie c ON t.id_categorie = c.id_categorie
          ORDER BY t.date_creation DESC";
$stmt = $pdo->query($query);
$tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Tickets</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="logo">Helpdesk - Administration</div>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="utilisateurs.php">Utilisateurs</a>
            <a href="categories.php">Catégories</a>
            <a href="tickets.php" class="active">Tickets</a>
            <a href="commentaires.php">Commentaires</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>Gestion des Tickets</h2>
                <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">
                    Vue d'ensemble de tous les tickets d'assistance enregistrés dans le système.
                </p>
            </div>
        </div>

        <?php if (!empty($message_succes)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message_succes) ?></div>
        <?php endif; ?>

        <?php if (!empty($message_erreur)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($message_erreur) ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Auteur</th>
                        <th>Date création</th>
                        <th>Statut</th>
                        <th>Commentaires</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #64748b; padding: 25px;">
                                Aucun ticket enregistré.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $t): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($t['id_ticket']) ?></td>
                                <td><strong><?= htmlspecialchars($t['titre']) ?></strong></td>
                                <td><?= htmlspecialchars($t['nom_categorie'] ?? 'Sans catégorie') ?></td>
                                <td><?= htmlspecialchars(($t['prenom'] ?? '') . ' ' . ($t['nom'] ?? 'Inconnu')) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($t['date_creation'])) ?></td>
                                <td>
                                    <?php
                                        $statusClass = 'badge-ouvert';
                                        if ($t['statut'] === 'En cours') $statusClass = 'badge-en-cours';
                                        elseif ($t['statut'] === 'Résolu') $statusClass = 'badge-resolu';
                                        elseif ($t['statut'] === 'Fermé') $statusClass = 'badge-ferme';
                                    ?>
                                    <span class="badge-statut <?= $statusClass ?>"><?= htmlspecialchars($t['statut']) ?></span>
                                </td>
                                <td>
                                    <a href="commentaires.php?id_ticket=<?= $t['id_ticket'] ?>" class="btn btn-secondaire" style="padding: 4px 10px; font-size: 0.8rem;">
                                        Voir (<?= $t['nb_commentaires'] ?>)
                                    </a>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="tickets.php?action=supprimer&id=<?= $t['id_ticket'] ?>" 
                                           class="btn btn-supprimer"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce ticket et tous ses commentaires ?');">
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
    </div>

</body>
</html>
