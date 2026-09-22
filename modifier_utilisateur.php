<?php
// modifier_utilisateur.php - Formulaire de modification d'un utilisateur
require_once 'config.php';

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

// Variables initialisées avec les données existantes
$nom = $utilisateur['nom'];
$prenom = $utilisateur['prenom'];
$email = $utilisateur['email'];
$role = $utilisateur['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $role = trim($_POST['role'] ?? '');

    $roles_autorises = ['Client', 'Support', 'Admin'];

    // 1. Validation : champs obligatoires (nom, prénom, email, rôle)
    if (empty($nom) || empty($prenom) || empty($email) || empty($role)) {
        $erreur = "Les champs nom, prénom, email et rôle sont obligatoires.";
    }
    // 2. Validation : format email valide
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    }
    // 3. Validation : rôle valide
    elseif (!in_array($role, $roles_autorises)) {
        $erreur = "Le rôle sélectionné n'est pas valide.";
    } else {
        // 4. Validation : email unique (en excluant l'utilisateur actuel)
        $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ? AND id_utilisateur != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            $erreur = "Cette adresse email est déjà utilisée par un autre compte.";
        } else {
            // 5. Gestion du mot de passe optionnel
            if (!empty($mot_de_passe)) {
                // Le mot de passe est renseigné : on le hashe et on le met à jour
                $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, role = ?, mot_de_passe = ? WHERE id_utilisateur = ?");
                $stmt->execute([$nom, $prenom, $email, $role, $mot_de_passe_hash, $id]);
            } else {
                // Le mot de passe est vide : on conserve l'ancien mot de passe
                $stmt = $pdo->prepare("UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, role = ? WHERE id_utilisateur = ?");
                $stmt->execute([$nom, $prenom, $email, $role, $id]);
            }

            // Redirection vers la liste des utilisateurs
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
    <title>Helpdesk - Modifier un utilisateur</title>
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

            <div class="form-group">
                <label for="role">Rôle :</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="Client" <?= $role === 'Client' ? 'selected' : '' ?>>Client</option>
                    <option value="Support" <?= $role === 'Support' ? 'selected' : '' ?>>Support</option>
                    <option value="Admin" <?= $role === 'Admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-modifier">Enregistrer les modifications</button>
                <a href="utilisateurs.php" class="btn btn-secondaire">Annuler</a>
            </div>
        </form>
    </div>

</body>
</html>
