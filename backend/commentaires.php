<?php
// commentaires.php - Consultation et suppression des commentaires (Admin)
require_once __DIR__ . '/config.php';

$message_erreur = '';
$message_succes = '';
$id_ticket_filtre = isset($_GET['id_ticket']) ? (int)$_GET['id_ticket'] : 0;

// Traitement de la suppression d'un commentaire
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id_del = (int)$_GET['id'];

    try {
        $stmtDel = $pdo->prepare("DELETE FROM commentaire WHERE id_commentaire = ?");
        $stmtDel->execute([$id_del]);

        $redirectUrl = "commentaires.php?success=deleted";
        if ($id_ticket_filtre > 0) {
            $redirectUrl .= "&id_ticket=" . $id_ticket_filtre;
        }
        header("Location: " . $redirectUrl);
        exit;
    } catch (PDOException $e) {
        $message_erreur = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'deleted') {
        $message_succes = "Commentaire supprimé avec succès.";
    }
}

// Récupération des informations du ticket si filtré
$ticket_info = null;
if ($id_ticket_filtre > 0) {
    $stmtT = $pdo->prepare("SELECT id_ticket, titre FROM ticket WHERE id_ticket = ?");
    $stmtT->execute([$id_ticket_filtre]);
    $ticket_info = $stmtT->fetch();
}

// Récupération des commentaires (tous ou filtrés par ticket)
if ($id_ticket_filtre > 0) {
    $query = "SELECT c.*, u.nom, u.prenom, t.titre AS titre_ticket 
              FROM commentaire c 
              LEFT JOIN utilisateur u ON c.id_utilisateur = u.id_utilisateur 
              LEFT JOIN ticket t ON c.id_ticket = t.id_ticket 
              WHERE c.id_ticket = ? 
              ORDER BY c.date_commentaire DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id_ticket_filtre]);
} else {
    $query = "SELECT c.*, u.nom, u.prenom, t.titre AS titre_ticket 
              FROM commentaire c 
              LEFT JOIN utilisateur u ON c.id_utilisateur = u.id_utilisateur 
              LEFT JOIN ticket t ON c.id_ticket = t.id_ticket 
              ORDER BY c.date_commentaire DESC";
    $stmt = $pdo->query($query);
}
$commentaires = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Commentaires</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="logo">Helpdesk - Administration</div>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="utilisateurs.php">Utilisateurs</a>
            <a href="categories.php">Catégories</a>
            <a href="tickets.php">Tickets</a>
            <a href="commentaires.php" class="active">Commentaires</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>
                    Gestion des Commentaires
                    <?php if ($ticket_info): ?>
                        - Ticket #<?= htmlspecialchars($ticket_info['id_ticket']) ?>
                    <?php endif; ?>
                </h2>
                <?php if ($ticket_info): ?>
                    <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">
                        Affichage des commentaires liés au ticket : <em>« <?= htmlspecialchars($ticket_info['titre']) ?> »</em>
                        | <a href="commentaires.php" style="color: #2563eb;">Voir tous les commentaires</a>
                    </p>
                <?php else: ?>
                    <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">
                        Historique et modération de tous les commentaires échangés sur la plateforme.
                    </p>
                <?php endif; ?>
            </div>
            <?php if ($ticket_info): ?>
                <a href="tickets.php" class="btn btn-secondaire">&larr; Retour aux tickets</a>
            <?php endif; ?>
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
                        <th>Ticket associé</th>
                        <th>Auteur</th>
                        <th>Date</th>
                        <th>Contenu du commentaire</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($commentaires)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; padding: 25px;">
                                Aucun commentaire enregistré.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($commentaires as $comm): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($comm['id_commentaire']) ?></td>
                                <td>
                                    <strong>#<?= htmlspecialchars($comm['id_ticket']) ?></strong>
                                    <span style="color: #64748b; font-size: 0.85rem; display: block;">
                                        <?= htmlspecialchars($comm['titre_ticket'] ?? 'Ticket supprimé') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars(($comm['prenom'] ?? '') . ' ' . ($comm['nom'] ?? 'Inconnu')) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($comm['date_commentaire'])) ?></td>
                                <td style="max-width: 350px;">
                                    <?= nl2br(htmlspecialchars($comm['contenu'])) ?>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <?php 
                                            $supprUrl = "commentaires.php?action=supprimer&id=" . $comm['id_commentaire'];
                                            if ($id_ticket_filtre > 0) {
                                                $supprUrl .= "&id_ticket=" . $id_ticket_filtre;
                                            }
                                        ?>
                                        <a href="<?= $supprUrl ?>" 
                                           class="btn btn-supprimer"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce commentaire ?');">
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
