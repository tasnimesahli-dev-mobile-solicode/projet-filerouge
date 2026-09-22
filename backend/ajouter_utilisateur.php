<?php
// ajouter_utilisateur.php - Formulaire d'ajout d'un utilisateur (Admin)
require_once __DIR__ . '/config.php';

$erreur = '';
$nom = '';
$prenom = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    // Validation des champs
    if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    } else {
        // Validation unicité de l'email
        $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreur = "Cette adresse email est déjà utilisée par un autre compte.";
        } else {
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Insertion de l'utilisateur avec le rôle Admin
            $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, 'Admin')");
            $stmt->execute([$nom, $prenom, $email, $mot_de_passe_hash]);

            header("Location: utilisateurs.php?success=added");
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
    <title>Administration - Ajouter un utilisateur</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="logo">Helpdesk - Administration</div>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="utilisateurs.php" class="active">Utilisateurs</a>
            <a href="categories.php">Catégories</a>
            <a href="tickets.php">Tickets</a>
            <a href="commentaires.php">Commentaires</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h2>Ajouter un nouvel utilisateur</h2>
            <a href="utilisateurs.php" class="btn btn-secondaire">&larr; Retour à la liste</a>
        </div>

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST" action="ajouter_utilisateur.php">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($nom) ?>" required>
            </div>

            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($prenom) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Adresse Email :</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-ajouter">Enregistrer l'utilisateur</button>
                <a href="utilisateurs.php" class="btn btn-secondaire">Annuler</a>
            </div>
        </form>
    </div>

</body>
</html>
