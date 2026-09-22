<?php
// ajouter_utilisateur.php - Formulaire d'ajout d'un utilisateur
require_once 'config.php';

$erreur = '';
$nom = '';
$prenom = '';
$email = '';
$role = 'Client';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $role = trim($_POST['role'] ?? '');

    $roles_autorises = ['Client', 'Support', 'Admin'];

    // 1. Validation : champs non vides
    if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe) || empty($role)) {
        $erreur = "Tous les champs sont obligatoires.";
    }
    // 2. Validation : format email valide
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    }
    // 3. Validation : rôle valide
    elseif (!in_array($role, $roles_autorises)) {
        $erreur = "Le rôle sélectionné n'est pas valide.";
    } else {
        // 4. Validation : unicité de l'email
        $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreur = "Cette adresse email est déjà utilisée par un autre compte.";
        } else {
            // Hashage du mot de passe
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Insertion en base de données avec requête préparée
            $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $mot_de_passe_hash, $role]);

            // Redirection vers la liste des utilisateurs
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
    <title>Helpdesk - Ajouter un utilisateur</title>
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

            <div class="form-group">
                <label for="role">Rôle :</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="Client" <?= $role === 'Client' ? 'selected' : '' ?>>Client</option>
                    <option value="Support" <?= $role === 'Support' ? 'selected' : '' ?>>Support</option>
                    <option value="Admin" <?= $role === 'Admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-ajouter">Enregistrer l'utilisateur</button>
                <a href="utilisateurs.php" class="btn btn-secondaire">Annuler</a>
            </div>
        </form>
    </div>

</body>
</html>
