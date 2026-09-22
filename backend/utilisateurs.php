<?php
// utilisateurs.php - Liste et suppression des utilisateurs
require_once 'config.php';

$message_erreur = '';
$message_succes = '';

// Traitement de la suppression
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id_a_supprimer = (int)$_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$id_a_supprimer]);
        header("Location: utilisateurs.php?success=deleted");
        exit;
    } catch (PDOException $e) {
        $message_erreur = "Impossible de supprimer cet utilisateur : il est associé à d'autres enregistrements (tickets ou commentaires).";
    }
}

// Messages via URL
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'added') {
        $message_succes = "Utilisateur ajouté avec succès.";
    } elseif ($_GET['success'] === 'updated') {
        $message_succes = "Utilisateur modifié avec succès.";
    } elseif ($_GET['success'] === 'deleted') {
        $message_succes = "Utilisateur supprimé avec succès.";
    }
}

// Récupération de la liste de tous les utilisateurs
$stmt = $pdo->query("SELECT id_utilisateur, nom, prenom, email, role FROM utilisateur ORDER BY id_utilisateur ASC");
$utilisateurs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpdesk - Gestion des Utilisateurs</title>
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
            <h2>Liste des Utilisateurs</h2>
            <a href="ajouter_utilisateur.php" class="btn btn-ajouter">+ Ajouter un utilisateur</a>
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
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($utilisateurs)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b;">Aucun utilisateur trouvé.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($utilisateurs as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id_utilisateur']) ?></td>
                                <td><?= htmlspecialchars($user['nom']) ?></td>
                                <td><?= htmlspecialchars($user['prenom']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <?php 
                                        $badgeClass = 'badge-client';
                                        if ($user['role'] === 'Support') {
                                            $badgeClass = 'badge-support';
                                        } elseif ($user['role'] === 'Admin') {
                                            $badgeClass = 'badge-admin';
                                        }
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($user['role']) ?></span>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="modifier_utilisateur.php?id=<?= urlencode($user['id_utilisateur']) ?>" class="btn btn-modifier">Modifier</a>
                                        <a href="utilisateurs.php?action=supprimer&id=<?= urlencode($user['id_utilisateur']) ?>" 
                                           class="btn btn-supprimer" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
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
