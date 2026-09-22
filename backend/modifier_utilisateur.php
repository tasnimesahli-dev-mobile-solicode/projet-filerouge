<?php
// modifier_utilisateur.php - Formulaire de modification d'un utilisateur (Admin)
require_once __DIR__ . '/config.php';

$erreur = '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: utilisateurs.php");
    exit;
}

// Récupération des données actuelles de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();

if (!$utilisateur) {
    header("Location: utilisateurs.php");
    exit;
}

$nom = $utilisateur['nom'];
$prenom = $utilisateur['prenom'];
$email = $utilisateur['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    // Validation des champs
    if (empty($nom) || empty($prenom) || empty($email)) {
        $erreur = "Les champs nom, prénom et email sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    } else {
        // Validation unicité email
        $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ? AND id_utilisateur != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            $erreur = "Cette adresse email est déjà utilisée par un autre compte.";
        } else {
            // Mot de passe optionnel
            if (!empty($mot_de_passe)) {
                $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, mot_de_passe = ? WHERE id_utilisateur = ?");
                $stmt->execute([$nom, $prenom, $email, $mot_de_passe_hash, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE utilisateur SET nom = ?, prenom = ?, email = ? WHERE id_utilisateur = ?");
                $stmt->execute([$nom, $prenom, $email, $id]);
            }

            header("Location: utilisateurs.php?success=updated");
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
    <title>Administration - Modifier un utilisateur</title>
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
            <h2>Modifier l'utilisateur #<?= htmlspecialchars($id) ?></h2>
            <a href="utilisateurs.php" class="btn btn-secondaire">&larr; Retour à la liste</a>
        </div>

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST" action="modifier_utilisateur.php?id=<?= urlencode($id) ?>">
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
                <label for="mot_de_passe">Nouveau mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control">
                <div class="form-hint">Laisser vide pour conserver le mot de passe actuel.</div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-modifier">Enregistrer les modifications</button>
                <a href="utilisateurs.php" class="btn btn-secondaire">Annuler</a>
            </div>
        </form>
    </div>

</body>
</html>
