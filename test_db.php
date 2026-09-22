<?php
require_once 'backend/config.php';
try {
    echo "Tables in helpdesk:\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    print_r($tables);

    echo "\nCategories:\n";
    $cats = $pdo->query("SELECT * FROM categorie")->fetchAll();
    print_r($cats);

    echo "\nTickets:\n";
    $tickets = $pdo->query("SELECT * FROM ticket")->fetchAll();
    print_r($tickets);

    echo "\nCommentaires:\n";
    $comms = $pdo->query("SELECT * FROM commentaire")->fetchAll();
    print_r($comms);

    echo "\nUtilisateurs:\n";
    $users = $pdo->query("SELECT id_utilisateur, nom, prenom, role FROM utilisateur")->fetchAll();
    print_r($users);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
