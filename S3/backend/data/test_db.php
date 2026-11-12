<?php
$passwords = ['12345678', ''];
$connected = false;
foreach ($passwords as $pwd) {
    try {
        $pdo = new PDO('mysql:host=localhost;port=3306;dbname=helpdesk;charset=utf8mb4', 'root', $pwd);
        echo "Connexion OK avec mot de passe: [{$pwd}]\n";
        $r = $pdo->query('SELECT COUNT(*) FROM categorie');
        echo "Catégories: " . $r->fetchColumn() . "\n";
        $r2 = $pdo->query('SELECT COUNT(*) FROM ticket');
        echo "Tickets: " . $r2->fetchColumn() . "\n";
        $connected = true;
        break;
    } catch (PDOException $e) {
        echo "Echec [{$pwd}]: " . $e->getMessage() . "\n";
    }
}
if (!$connected) {
    echo "AUCUNE CONNEXION POSSIBLE - Vérifier MySQL\n";
}

