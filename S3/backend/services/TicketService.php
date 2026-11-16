<?php
// S3/backend/services/TicketService.php - Tier 2 : Dépendances tickets par catégorie
// Exception inline : HasDependenciesException

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategorieDAO.php';

// ── Exception Métier ──────────────────────────────────────────────────────────

class HasDependenciesException extends RuntimeException
{
    private int $ticketCount;

    public function __construct(int $count)
    {
        $this->ticketCount = $count;
        parent::__construct(
            "Impossible de supprimer cette catégorie car {$count} ticket(s) y sont actuellement associés."
        );
    }

    public function getTicketCount(): int { return $this->ticketCount; }
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

    public function countByCategorie(int $categorieId): int
    {
        return $this->dao->countTicketsByCategorie($categorieId);
    }

    public function assertNoDependencies(int $categorieId): void
    {
        $count = $this->countByCategorie($categorieId);
        if ($count > 0) {
            throw new HasDependenciesException($count);
        }
    }
}

