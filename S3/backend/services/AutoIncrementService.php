<?php
// S3/backend/services/AutoIncrementService.php
// Tier 2 — Gestion des IDs consécutifs

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategorieDAO.php';

class AutoIncrementService extends BaseService
{
    private CategorieDAO $dao;

    public function __construct(PDO $pdo, CategorieDAO $dao)
    {
        parent::__construct($pdo);
        $this->dao = $dao;
    }

    /**
     * Prochain ID consécutif (MAX + 1) — évite les sauts d'ID.
     */
    public function getNextId(): int
    {
        return $this->dao->getMaxId();
    }

    /**
     * Réajuste l'AUTO_INCREMENT après insert ou delete.
     */
    public function resetAutoIncrement(int $nextId): void
    {
        $this->dao->resetAutoIncrement($nextId);
    }
}

