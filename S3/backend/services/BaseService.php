<?php
// S3/backend/services/BaseService.php - Tier 2 : Service de base abstrait

abstract class BaseService
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}

