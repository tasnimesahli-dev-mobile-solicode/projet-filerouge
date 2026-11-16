<?php
// S3/backend/classes/Database.php - Singleton PDO
// Architecture 3 Tiers - Couche Support

class Database
{
    private static ?PDO $instance = null;

    private string $host      = 'localhost';
    private string $dbname    = 'helpdesk';
    private string $user      = 'root';
    private array  $passwords = ['12345678', ''];
    private int    $port      = 3306;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $db = new self();
            self::$instance = $db->connect();
        }
        return self::$instance;
    }

    private function connect(): PDO
    {
        $lastException = null;

        foreach ($this->passwords as $pwd) {
            try {
                $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
                $pdo = new PDO($dsn, $this->user, $pwd, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
                return $pdo;
            } catch (PDOException $e) {
                $lastException = $e;
            }
        }

        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Erreur de connexion à la base de données : ' . ($lastException ? $lastException->getMessage() : 'Impossible de se connecter')
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

