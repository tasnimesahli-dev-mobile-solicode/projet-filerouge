<?php
// S3/backend/dao/TicketDAO.php
// Tier 3 — Data Access : requêtes SQL pour la table `ticket`

require_once __DIR__ . '/../classes/Ticket.php';

class TicketDAO
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Tous les tickets avec détails (nom catégorie, nom utilisateur).
     */
    public function findAllWithDetails(): array
    {
        $stmt = $this->pdo->query("
            SELECT t.*,
                   c.nom_categorie,
                   CONCAT(u.prenom, ' ', u.nom) AS nom_complet_utilisateur
            FROM   ticket t
            JOIN   categorie   c ON t.id_categorie   = c.id_categorie
            JOIN   utilisateur u ON t.id_utilisateur  = u.id_utilisateur
            ORDER BY t.date_creation DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Un ticket avec détails par son ID, ou null.
     */
    public function findByIdWithDetails(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT t.*,
                   c.nom_categorie,
                   CONCAT(u.prenom, ' ', u.nom) AS nom_complet_utilisateur
            FROM   ticket t
            JOIN   categorie   c ON t.id_categorie   = c.id_categorie
            JOIN   utilisateur u ON t.id_utilisateur  = u.id_utilisateur
            WHERE  t.id_ticket = ?
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Un ticket simple (entité Ticket) par son ID, ou null.
     */
    public function findById(int $id): ?Ticket
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ticket WHERE id_ticket = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return new Ticket(
            (int)$row['id_ticket'],
            $row['titre'],
            $row['description'],
            $row['statut'],
            (int)$row['id_utilisateur'],
            (int)$row['id_categorie'],
            $row['date_creation']
        );
    }

    /**
     * Insertion d'un nouveau ticket.
     */
    public function insert(Ticket $t): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO ticket (titre, description, statut, id_utilisateur, id_categorie)
             VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $t->getTitre(),
            $t->getDescription(),
            $t->getStatut(),
            $t->getIdUtilisateur(),
            $t->getIdCategorie(),
        ]);
    }

    /**
     * Mise à jour d'un ticket existant.
     */
    public function update(Ticket $t): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE ticket
             SET titre = ?, description = ?, statut = ?, id_utilisateur = ?, id_categorie = ?
             WHERE id_ticket = ?"
        );
        return $stmt->execute([
            $t->getTitre(),
            $t->getDescription(),
            $t->getStatut(),
            $t->getIdUtilisateur(),
            $t->getIdCategorie(),
            $t->getIdTicket(),
        ]);
    }

    /**
     * Suppression par ID.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM ticket WHERE id_ticket = ?");
        return $stmt->execute([$id]);
    }
}

