<?php
// S3/backend/services/BaseService.php
// Tier 2 — Classe abstraite parente de tous les Services
// Fournit l'accès PDO partagé via injection de dépendance

abstract class BaseService
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}

