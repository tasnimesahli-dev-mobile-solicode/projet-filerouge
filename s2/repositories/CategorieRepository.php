<?php
// fileRouge-s2/repositories/CategorieRepository.php - Couche d'accès aux données (POO)

require_once __DIR__ . '/../models/Categorie.php';

class CategorieRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère toutes les catégories avec le nombre de tickets rattachés
     * @return array
     */
    public function findAllWithTicketCount(): array
    {
        $sql = "
            SELECT 
                c.id_categorie,
                c.nom_categorie,
                COUNT(t.id_ticket) AS nb_tickets
            FROM categorie c
            LEFT JOIN ticket t ON c.id_categorie = t.id_categorie
            GROUP BY c.id_categorie, c.nom_categorie
            ORDER BY c.id_categorie ASC
        ";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Récupère une catégorie par son ID
     * @param int $id
     * @return Categorie|null
     */
    public function findById(int $id): ?Categorie
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categorie WHERE id_categorie = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Categorie((int)$row['id_categorie'], $row['nom_categorie']);
    }

    /**
     * Recherche une catégorie par son nom (avec exclusion optionnelle d'un ID pour l'update)
     * @param string $name
     * @param int|null $excludeId
     * @return Categorie|null
     */
    public function findByName(string $name, ?int $excludeId = null): ?Categorie
    {
        if ($excludeId !== null) {
            $stmt = $this->pdo->prepare("SELECT * FROM categorie WHERE nom_categorie = ? AND id_categorie != ?");
            $stmt->execute([$name, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM categorie WHERE nom_categorie = ?");
            $stmt->execute([$name]);
        }

        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        return new Categorie((int)$row['id_categorie'], $row['nom_categorie']);
    }

    /**
     * Vérifie le nombre de tickets rattachés à une catégorie
     * @param int $id
     * @return int
     */
    public function countTickets(int $id): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM ticket WHERE id_categorie = ?");
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Crée une nouvelle catégorie avec génération d'ID consécutif (1, 2, 3, 4, 5...)
     * @param Categorie $categorie
     * @return Categorie
     */
    public function create(Categorie $categorie): Categorie
    {
        // Déterminer le prochain ID consécutif pour éviter les sauts aléatoires (ex: 44)
        $stmtMax = $this->pdo->query("SELECT COALESCE(MAX(id_categorie), 0) + 1 AS next_id FROM categorie");
        $nextId = (int)$stmtMax->fetchColumn();

        $stmt = $this->pdo->prepare("INSERT INTO categorie (id_categorie, nom_categorie) VALUES (?, ?)");
        $stmt->execute([$nextId, $categorie->getNomCategorie()]);

        $categorie->setIdCategorie($nextId);

        // Synchronisation de l'AUTO_INCREMENT de la table pour les futures opérations
        try {
            $this->pdo->exec("ALTER TABLE categorie AUTO_INCREMENT = " . ($nextId + 1));
        } catch (Exception $e) {
            // Ignorer si le moteur MySQL restreint la modification
        }

        return $categorie;
    }

    /**
     * Met à jour une catégorie existante
     * @param Categorie $categorie
     * @return bool
     */
    public function update(Categorie $categorie): bool
    {
        $stmt = $this->pdo->prepare("UPDATE categorie SET nom_categorie = ? WHERE id_categorie = ?");
        return $stmt->execute([
            $categorie->getNomCategorie(),
            $categorie->getIdCategorie()
        ]);
    }

    /**
     * Supprime une catégorie par son ID
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM categorie WHERE id_categorie = ?");
        $success = $stmt->execute([$id]);

        if ($success) {
            // Réajuster l'AUTO_INCREMENT au prochain ID disponible
            try {
                $stmtMax = $this->pdo->query("SELECT COALESCE(MAX(id_categorie), 0) + 1 FROM categorie");
                $nextId = (int)$stmtMax->fetchColumn();
                $this->pdo->exec("ALTER TABLE categorie AUTO_INCREMENT = $nextId");
            } catch (Exception $e) {
                // Ignorer si le moteur MySQL restreint la modification
            }
        }

        return $success;
    }
}
