<?php
// categories.php - Gestion des catégories (Admin)
require_once __DIR__ . '/config.php';

$message_erreur = '';
$message_succes = '';

// Suppression d'une catégorie
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Vérifier si des tickets sont associés à cette catégorie
    $stmtVerif = $pdo->prepare("SELECT COUNT(*) FROM ticket WHERE id_categorie = ?");
    $stmtVerif->execute([$id]);
    $nbTickets = $stmtVerif->fetchColumn();

    if ($nbTickets > 0) {
        $message_erreur = "Impossible de supprimer cette catégorie : $nbTickets ticket(s) y sont actuellement associés.";
    } else {
        try {
            $stmtDel = $pdo->prepare("DELETE FROM categorie WHERE id_categorie = ?");
            $stmtDel->execute([$id]);
            header("Location: categories.php?success=deleted");
            exit;
        } catch (PDOException $e) {
            $message_erreur = "Erreur lors de la suppression : " . $e->getMessage();
        }
    }
}

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'added') {
        $message_succes = "Catégorie ajoutée avec succès.";
    } elseif ($_GET['success'] === 'updated') {
        $message_succes = "Catégorie modifiée avec succès.";
    } elseif ($_GET['success'] === 'deleted') {
        $message_succes = "Catégorie supprimée avec succès.";
    }
}

// Récupération des catégories avec le nombre de tickets liés
$stmt = $pdo->query("SELECT c.*, COUNT(t.id_ticket) AS nb_tickets 
                     FROM categorie c 
                     LEFT JOIN ticket t ON c.id_categorie = t.id_categorie 
                     GROUP BY c.id_categorie 
                     ORDER BY c.nom_categorie ASC");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Catégories</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="logo">Helpdesk - Administration</div>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="utilisateurs.php">Utilisateurs</a>
            <a href="categories.php" class="active">Catégories</a>
            <a href="tickets.php">Tickets</a>
            <a href="commentaires.php">Commentaires</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h2>Gestion des Catégories</h2>
            <a href="ajouter_categorie.php" class="btn btn-ajouter">+ Ajouter une catégorie</a>
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
                        <th>Nom de la catégorie</th>
                        <th>Nombre de tickets</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b;">Aucune catégorie enregistrée.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($cat['id_categorie']) ?></td>
                                <td><strong><?= htmlspecialchars($cat['nom_categorie']) ?></strong></td>
                                <td><?= htmlspecialchars($cat['nb_tickets']) ?></td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="modifier_categorie.php?id=<?= $cat['id_categorie'] ?>" class="btn btn-modifier">Modifier</a>
                                        <a href="categories.php?action=supprimer&id=<?= $cat['id_categorie'] ?>" 
                                           class="btn btn-supprimer"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
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
