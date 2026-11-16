<?php
// backend/ajouter_ticket.php - Page Admin : Ajouter un Ticket (Create)
require_once __DIR__ . '/config.php';

$erreur = '';
$titre = '';
$description = '';
$id_categorie = 0;
$id_utilisateur = 0;
$statut = 'Ouvert';

// Récupération des catégories disponibles pour la liste déroulante
$categories = [];
$res_cat = mysqli_query($conn, "SELECT id_categorie, nom_categorie FROM categorie ORDER BY nom_categorie ASC");
if ($res_cat) {
    while ($row = mysqli_fetch_assoc($res_cat)) {
        $categories[] = $row;
    }
}

// Récupération des utilisateurs disponibles pour la liste déroulante
$utilisateurs = [];
$res_users = mysqli_query($conn, "SELECT id_utilisateur, nom, prenom, role FROM utilisateur ORDER BY nom ASC, prenom ASC");
if ($res_users) {
    while ($row = mysqli_fetch_assoc($res_users)) {
        $utilisateurs[] = $row;
    }
}

// Traitement de la soumission du formulaire (POST standard, sans Fetch API / AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id_categorie = (int)($_POST['id_categorie'] ?? 0);
    $id_utilisateur = (int)($_POST['id_utilisateur'] ?? 0);
    $statut = trim($_POST['statut'] ?? 'Ouvert');

    // Validation des champs
    $statuts_valides = ['Ouvert', 'En cours', 'Résolu', 'Fermé'];

    if (empty($titre)) {
        $erreur = "Le titre du ticket est obligatoire.";
    } elseif (mb_strlen($titre) > 200) {
        $erreur = "Le titre ne doit pas dépasser 200 caractères.";
    } elseif (empty($description)) {
        $erreur = "La description du problème est obligatoire.";
    } elseif ($id_categorie <= 0) {
        $erreur = "Veuillez sélectionner une catégorie valide.";
    } elseif ($id_utilisateur <= 0) {
        $erreur = "Veuillez sélectionner un utilisateur (auteur) valide.";
    } elseif (!in_array($statut, $statuts_valides, true)) {
        $erreur = "Le statut sélectionné est invalide.";
    } else {
        // Vérification de l'existence de l'utilisateur et de la catégorie
        $check_user = mysqli_query($conn, "SELECT id_utilisateur FROM utilisateur WHERE id_utilisateur = $id_utilisateur");
        $check_cat = mysqli_query($conn, "SELECT id_categorie FROM categorie WHERE id_categorie = $id_categorie");

        if (mysqli_num_rows($check_user) === 0) {
            $erreur = "L'utilisateur sélectionné n'existe pas.";
        } elseif (mysqli_num_rows($check_cat) === 0) {
            $erreur = "La catégorie sélectionnée n'existe pas.";
        } else {
            // Insertion avec requête préparée MySQLi
            $sql = "INSERT INTO ticket (titre, description, statut, id_utilisateur, id_categorie) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssii", $titre, $description, $statut, $id_utilisateur, $id_categorie);

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    header("Location: tickets.php?success=added");
                    exit;
                } else {
                    $erreur = "Erreur lors de l'enregistrement du ticket : " . mysqli_error($conn);
                    mysqli_stmt_close($stmt);
                }
            } else {
                $erreur = "Erreur de préparation de la requête : " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Ajouter un Ticket</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="logo">
            <span>🎫 Helpdesk</span>
            <span class="logo-badge">Admin</span>
        </div>
        <nav>
            <a href="tickets.php">Tickets</a>
            <a href="tickets.php" class="btn btn-secondaire btn-sm">&larr; Retour aux tickets</a>
        </nav>
    </header>

    <main class="container">
        <div class="page-header">
            <div>
                <h2>Ajouter un Nouveau Ticket</h2>
                <p class="page-header-subtitle">
                    Création d'un ticket d'assistance rattaché à un utilisateur et une catégorie.
                </p>
            </div>
            <div>
                <a href="tickets.php" class="btn btn-secondaire">&larr; Retour à la liste</a>
            </div>
        </div>

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger">
                <strong>Erreur :</strong> <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($utilisateurs)): ?>
            <div class="alert alert-danger">
                <strong>Attention :</strong> Aucun utilisateur n'a été trouvé dans la base de données. Vous devez avoir au moins un utilisateur pour pouvoir créer un ticket.
            </div>
        <?php endif; ?>

        <?php if (empty($categories)): ?>
            <div class="alert alert-danger">
                <strong>Attention :</strong> Aucune catégorie n'a été trouvée dans la base de données. Vous devez avoir au moins une catégorie pour pouvoir créer un ticket.
            </div>
        <?php endif; ?>

        <div class="form-card">
            <!-- Formulaire standard HTML en méthode POST (sans Fetch API ni AJAX) -->
            <form method="POST" action="ajouter_ticket.php">
                <div class="form-group">
                    <label for="titre">Titre du ticket <span class="required">*</span> :</label>
                    <input type="text" 
                           id="titre" 
                           name="titre" 
                           class="form-control" 
                           maxlength="200"
                           placeholder="Ex : Problème de connexion à l'imprimante réseau" 
                           value="<?= htmlspecialchars($titre) ?>" 
                           required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="id_categorie">Catégorie <span class="required">*</span> :</label>
                        <select id="id_categorie" name="id_categorie" class="form-control" required>
                            <option value="">-- Choisir une catégorie --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['id_categorie'] ?>" <?= ($id_categorie === (int)$cat['id_categorie']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom_categorie']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_utilisateur">Auteur / Demandeur <span class="required">*</span> :</label>
                        <select id="id_utilisateur" name="id_utilisateur" class="form-control" required>
                            <option value="">-- Choisir un utilisateur --</option>
                            <?php foreach ($utilisateurs as $u): ?>
                                <option value="<?= (int)$u['id_utilisateur'] ?>" <?= ($id_utilisateur === (int)$u['id_utilisateur']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?> (<?= htmlspecialchars($u['role']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="statut">Statut initial <span class="required">*</span> :</label>
                    <select id="statut" name="statut" class="form-control" required>
                        <option value="Ouvert" <?= ($statut === 'Ouvert') ? 'selected' : '' ?>>Ouvert</option>
                        <option value="En cours" <?= ($statut === 'En cours') ? 'selected' : '' ?>>En cours</option>
                        <option value="Résolu" <?= ($statut === 'Résolu') ? 'selected' : '' ?>>Résolu</option>
                        <option value="Fermé" <?= ($statut === 'Fermé') ? 'selected' : '' ?>>Fermé</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description détaillée du problème <span class="required">*</span> :</label>
                    <textarea id="description" 
                              name="description" 
                              class="form-control" 
                              rows="6" 
                              placeholder="Expliquez en détail le problème rencontré ou la demande d'assistance..." 
                              required><?= htmlspecialchars($description) ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-ajouter">
                        <span>💾</span> Créer le ticket
                    </button>
                    <a href="tickets.php" class="btn btn-secondaire">Annuler</a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
