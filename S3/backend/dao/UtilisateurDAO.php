<?php
// S3/backend/dao/UtilisateurDAO.php - Tier 3 : Data Access Object pour `utilisateur`

class UtilisateurDAO
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Tous les utilisateurs */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT id_utilisateur, nom, prenom, email, role
             FROM utilisateur
             ORDER BY nom ASC, prenom ASC"
        );
        return $stmt->fetchAll();
    }

    /** Vérifie l'existence d'un utilisateur par ID */
    public function existsById(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() !== false;
    }
}

