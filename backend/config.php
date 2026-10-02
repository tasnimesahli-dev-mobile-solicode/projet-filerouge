<?php
// backend/config.php - Connexion à la base de données avec MySQLi

$host = 'localhost';
$user = 'root';
$dbname = 'helpdesk';
$port = 3306;

// Liste des mots de passe courants (configuration locale / XAMPP / Laragon)
$passwords = ['12345678', ''];
$conn = false;

foreach ($passwords as $pwd) {
    $conn = @mysqli_connect($host, $user, $pwd, $dbname, $port);
    if ($conn) {
        break;
    }
}

if (!$conn) {
    die("Erreur de connexion à la base de données MySQL : " . mysqli_connect_error());
}

// Configuration du jeu de caractères en utf8mb4
mysqli_set_charset($conn, "utf8mb4");
?>
