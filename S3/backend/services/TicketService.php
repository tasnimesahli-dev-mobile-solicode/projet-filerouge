<?php
// S3/backend/services/TicketService.php
// Tier 2 — Gestion des tickets liés aux catégories
// Exception inline : HasDependenciesException

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategorieDAO.php';

// ── Exception ─────────────────────────────────────────────────────────────────

class HasDependenciesException extends RuntimeException
{
    private int $nbTickets;
    public function __construct(int $nbTickets)
    {
        $this->nbTickets = $nbTickets;
        parent::__construct(
            "Impossible de supprimer cette catégorie car {$nbTickets} ticket(s) y sont actuellement associés."
        );
    }
    public function getNbTickets(): int { return $this->nbTickets; }
}

// ── Service ───────────────────────────────────────────────────────────────────

class TicketService extends BaseService
{
    private CategorieDAO $dao;

    public function __construct(PDO $pdo, CategorieDAO $dao)
    {
        parent::__construct($pdo);
        $this->dao = $dao;
    }

    /** Nombre de tickets liés à une catégorie */
    public function countByCategorie(int $categorieId): int
    {
        return $this->dao->countTicketsByCategorie($categorieId);
    }

    /**
     * Lève une exception si des tickets sont rattachés à la catégorie.
     * @throws HasDependenciesException
     */
    public function assertNoDependencies(int $categorieId): void
    {
        $nb = $this->countByCategorie($categorieId);
        if ($nb > 0) {
            throw new HasDependenciesException($nb);
        }
    }
}

