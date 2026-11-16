<?php
// S3/backend/dao/CategorieDAO.php - Tier 3 : Data Access Object pour `categorie`
// SQL pur via PDO sans aucune logique métier

require_once __DIR__ . '/../classes/Categorie.php';

class CategorieDAO
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Toutes les catégories avec le nombre de tickets rattachés */
    public function findAllWithTicketCount(): array
    {
        $sql = "
            SELECT c.id_categorie, c.nom_categorie, c.description,
                   COUNT(t.id_ticket) AS nb_tickets
            FROM categorie c
            LEFT JOIN ticket t ON c.id_categorie = t.id_categorie
            GROUP BY c.id_categorie, c.nom_categorie, c.description
            ORDER BY c.id_categorie ASC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /** Récupère une catégorie par son ID */
    public function findById(int $id): ?Categorie
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categorie WHERE id_categorie = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row
            ? new Categorie((int)$row['id_categorie'], $row['nom_categorie'], $row['description'] ?? null)
            : null;
    }

    /** Recherche par nom avec exclusion optionnelle d'un ID */
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
        return $row
            ? new Categorie((int)$row['id_categorie'], $row['nom_categorie'], $row['description'] ?? null)
            : null;
    }

    /** Nombre de tickets rattachés à une catégorie */
    public function countTicketsByCategorie(int $id): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM ticket WHERE id_categorie = ?");
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn();
    }

    /** Prochain ID consécutif disponible (MAX + 1) */
    public function getMaxId(): int
    {
        $stmt = $this->pdo->query("SELECT COALESCE(MAX(id_categorie), 0) + 1 AS next_id FROM categorie");
        return (int)$stmt->fetchColumn();
    }

    /** Insertion avec ID explicite */
    public function insert(Categorie $c): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO categorie (id_categorie, nom_categorie, description) VALUES (?, ?, ?)");
        return $stmt->execute([$c->getIdCategorie(), $c->getNomCategorie(), $c->getDescription()]);
    }

    /** Mise à jour du nom et de la description */
    public function update(Categorie $c): bool
    {
        $stmt = $this->pdo->prepare("UPDATE categorie SET nom_categorie = ?, description = ? WHERE id_categorie = ?");
        return $stmt->execute([$c->getNomCategorie(), $c->getDescription(), $c->getIdCategorie()]);
    }

    /** Suppression par ID */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM categorie WHERE id_categorie = ?");
        return $stmt->execute([$id]);
    }

    /** Réajustement de l'AUTO_INCREMENT */
    public function resetAutoIncrement(int $nextId): void
    {
        try {
            $this->pdo->exec("ALTER TABLE categorie AUTO_INCREMENT = " . (int)$nextId);
        } catch (Exception $e) {
            // Ignoré si MySQL restreint
        }
    }
}

