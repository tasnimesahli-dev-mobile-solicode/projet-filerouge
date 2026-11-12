<?php
// S3/backend/api/utilisateurs.php
// Tier 1 — Lecture seule des utilisateurs (pour les selects du formulaire ticket)

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../dao/UtilisateurDAO.php';

try {
    $pdo  = Database::getInstance();
    $dao  = new UtilisateurDAO($pdo);
    $data = $dao->findAll();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'data' => $data, 'count' => count($data)], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

