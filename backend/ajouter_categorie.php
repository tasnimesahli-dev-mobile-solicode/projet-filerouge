<?php
// ajouter_categorie.php - Ajout d'une catégorie (Admin)
require_once __DIR__ . '/config.php';

$erreur = '';
$nom_categorie = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_categorie = trim($_POST['nom_categorie'] ?? '');

    if (empty($nom_categorie)) {
        $erreur = "Le nom de la catégorie est obligatoire.";
    } else {
        // Vérifier l'unicité
        $stmtCheck = $pdo->prepare("SELECT id_categorie FROM categorie WHERE nom_categorie = ?");
        $stmtCheck->execute([$nom_categorie]);
        if ($stmtCheck->fetch()) {
            $erreur = "Une catégorie portant ce nom existe déjà.";
        } else {
            $stmtInsert = $pdo->prepare("INSERT INTO categorie (nom_categorie) VALUES (?)");
            $stmtInsert->execute([$nom_categorie]);

            header("Location: categories.php?success=added");
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
    <title>Administration - Ajouter une Catégorie</title>
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
            <h2>Ajouter une Catégorie</h2>
            <a href="categories.php" class="btn btn-secondaire">&larr; Retour à la liste</a>
        </div>

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST" action="ajouter_categorie.php">
            <div class="form-group">
                <label for="nom_categorie">Nom de la catégorie :</label>
                <input type="text" id="nom_categorie" name="nom_categorie" class="form-control" placeholder="Ex: Matériel, Réseau..." value="<?= htmlspecialchars($nom_categorie) ?>" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-ajouter">Enregistrer la catégorie</button>
                <a href="categories.php" class="btn btn-secondaire">Annuler</a>
            </div>
        </form>
    </div>

</body>
</html>
