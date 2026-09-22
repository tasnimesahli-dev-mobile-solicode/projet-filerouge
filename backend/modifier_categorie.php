<?php
// modifier_categorie.php - Modification d'une catégorie (Admin)
require_once __DIR__ . '/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: categories.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM categorie WHERE id_categorie = ?");
$stmt->execute([$id]);
$categorie = $stmt->fetch();

if (!$categorie) {
    header("Location: categories.php");
    exit;
}

$erreur = '';
$nom_categorie = $categorie['nom_categorie'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_categorie = trim($_POST['nom_categorie'] ?? '');

    if (empty($nom_categorie)) {
        $erreur = "Le nom de la catégorie est obligatoire.";
    } else {
        // Vérifier l'unicité (sauf la catégorie actuelle)
        $stmtCheck = $pdo->prepare("SELECT id_categorie FROM categorie WHERE nom_categorie = ? AND id_categorie != ?");
        $stmtCheck->execute([$nom_categorie, $id]);
        if ($stmtCheck->fetch()) {
            $erreur = "Une autre catégorie portant ce nom existe déjà.";
        } else {
            $stmtUpdate = $pdo->prepare("UPDATE categorie SET nom_categorie = ? WHERE id_categorie = ?");
            $stmtUpdate->execute([$nom_categorie, $id]);

            header("Location: categories.php?success=updated");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Modifier une Catégorie</title>
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
            <h2>Modifier la Catégorie #<?= htmlspecialchars($id) ?></h2>
            <a href="categories.php" class="btn btn-secondaire">&larr; Retour à la liste</a>
        </div>

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST" action="modifier_categorie.php?id=<?= $id ?>">
            <div class="form-group">
                <label for="nom_categorie">Nom de la catégorie :</label>
                <input type="text" id="nom_categorie" name="nom_categorie" class="form-control" value="<?= htmlspecialchars($nom_categorie) ?>" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-modifier">Enregistrer les modifications</button>
                <a href="categories.php" class="btn btn-secondaire">Annuler</a>
            </div>
        </form>
    </div>

</body>
</html>
