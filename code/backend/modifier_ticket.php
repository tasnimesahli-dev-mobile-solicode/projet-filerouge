<?php
// backend/modifier_ticket.php - Page Admin : Modifier un Ticket (Update)
require_once __DIR__ . '/config.php';

$id_ticket = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_ticket <= 0) {
    header("Location: tickets.php");
    exit;
}

// Récupération des données actuelles du ticket avec requête préparée MySQLi
$stmt_select = mysqli_prepare($conn, "SELECT * FROM ticket WHERE id_ticket = ?");
if (!$stmt_select) {
    die("Erreur de préparation de la requête : " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt_select, "i", $id_ticket);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$ticket = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt_select);

if (!$ticket) {
    header("Location: tickets.php");
    exit;
}

$erreur = '';
$titre = $ticket['titre'];
$description = $ticket['description'];
$id_categorie = (int)$ticket['id_categorie'];
$id_utilisateur = (int)$ticket['id_utilisateur'];
$statut = $ticket['statut'];
$date_creation = $ticket['date_creation'];

// Récupération des catégories pour la liste déroulante
$categories = [];
$res_cat = mysqli_query($conn, "SELECT id_categorie, nom_categorie FROM categorie ORDER BY nom_categorie ASC");
if ($res_cat) {
    while ($row = mysqli_fetch_assoc($res_cat)) {
        $categories[] = $row;
    }
}

// Récupération des utilisateurs pour la liste déroulante
$utilisateurs = [];
$res_users = mysqli_query($conn, "SELECT id_utilisateur, nom, prenom, role FROM utilisateur ORDER BY nom ASC, prenom ASC");
if ($res_users) {
    while ($row = mysqli_fetch_assoc($res_users)) {
        $utilisateurs[] = $row;
    }
}

// Traitement de la mise à jour (POST standard, sans Fetch API ni AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id_categorie = (int)($_POST['id_categorie'] ?? 0);
    $id_utilisateur = (int)($_POST['id_utilisateur'] ?? 0);
    $statut = trim($_POST['statut'] ?? 'Ouvert');

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
            // Mise à jour sécurisée avec requête préparée MySQLi
            $sql = "UPDATE ticket SET titre = ?, description = ?, statut = ?, id_utilisateur = ?, id_categorie = ? WHERE id_ticket = ?";
            $stmt_update = mysqli_prepare($conn, $sql);

            if ($stmt_update) {
                mysqli_stmt_bind_param($stmt_update, "sssiii", $titre, $description, $statut, $id_utilisateur, $id_categorie, $id_ticket);

                if (mysqli_stmt_execute($stmt_update)) {
                    mysqli_stmt_close($stmt_update);
                    header("Location: tickets.php?success=updated");
                    exit;
                } else {
                    $erreur = "Erreur lors de la modification du ticket : " . mysqli_error($conn);
                    mysqli_stmt_close($stmt_update);
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
    <title>Administration - Modifier le Ticket #<?= $id_ticket ?></title>
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
                <h2>Modifier le Ticket #<?= $id_ticket ?></h2>
                <p class="page-header-subtitle">
                    Modifiez les informations, le statut ou la catégorie de ce ticket.
                    <span style="display: block; margin-top: 2px; font-size: 0.82rem; color: #94a3b8;">
                        Créé le <?= date('d/m/Y à H:i', strtotime($date_creation)) ?>
                    </span>
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

        <div class="form-card">
            <!-- Formulaire standard HTML en méthode POST (sans Fetch API ni AJAX) -->
            <form method="POST" action="modifier_ticket.php?id=<?= $id_ticket ?>">
                <div class="form-group">
                    <label for="titre">Titre du ticket <span class="required">*</span> :</label>
                    <input type="text" 
                           id="titre" 
                           name="titre" 
                           class="form-control" 
                           maxlength="200"
                           value="<?= htmlspecialchars($titre) ?>" 
                           required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="id_categorie">Catégorie <span class="required">*</span> :</label>
                        <select id="id_categorie" name="id_categorie" class="form-control" required>
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
                            <?php foreach ($utilisateurs as $u): ?>
                                <option value="<?= (int)$u['id_utilisateur'] ?>" <?= ($id_utilisateur === (int)$u['id_utilisateur']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?> (<?= htmlspecialchars($u['role']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="statut">Statut du ticket <span class="required">*</span> :</label>
                    <select id="statut" name="statut" class="form-control" required>
                        <option value="Ouvert" <?= ($statut === 'Ouvert') ? 'selected' : '' ?>>Ouvert</option>
                        <option value="En cours" <?= ($statut === 'En cours') ? 'selected' : '' ?>>En cours</option>
                        <option value="Résolu" <?= ($statut === 'Résolu') ? 'selected' : '' ?>>Résolu</option>
                        <option value="Fermé" <?= ($statut === 'Fermé') ? 'selected' : '' ?>>Fermé</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description détaillée <span class="required">*</span> :</label>
                    <textarea id="description" 
                              name="description" 
                              class="form-control" 
                              rows="6" 
                              required><?= htmlspecialchars($description) ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-modifier">
                        <span>✏️</span> Enregistrer les modifications
                    </button>
                    <a href="tickets.php" class="btn btn-secondaire">Annuler</a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
