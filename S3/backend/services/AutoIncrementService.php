<?php
// S3/backend/services/AutoIncrementService.php - Tier 2 : IDs consécutifs

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

    public function getNextId(): int
    {
        return $this->dao->getMaxId();
    }

    public function resetAutoIncrement(int $nextId): void
    {
        $this->dao->resetAutoIncrement($nextId);
    }
}

