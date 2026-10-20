<?php
// fileRouge-s2/api/categories.php - Point d'entrée de l'API RESTful Catégories (JSON)

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Gestion des requêtes préliminaires CORS OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../repositories/CategorieRepository.php';
require_once __DIR__ . '/../controllers/CategorieController.php';

// Initialisation POO
$pdo = Database::getInstance();
$repository = new CategorieRepository($pdo);
$controller = new CategorieController($repository);

// Détection de la méthode HTTP (avec support du paramètre _method)
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

// Extraction des données du corps de la requête (JSON ou Form-Data)
$rawBody = file_get_contents('php://input');
$body = [];
if (!empty($rawBody)) {
    $decoded = json_decode($rawBody, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $body = $decoded;
    }
}
if (empty($body) && !empty($_POST)) {
    $body = $_POST;
}

// Récupération de l'ID éventuel
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Routage RESTful
switch ($method) {
    case 'GET':
        if ($id !== null && $id > 0) {
            $controller->show($id);
        } else {
            $controller->index();
        }
        break;

    case 'POST':
        $controller->store($body);
        break;

    case 'PUT':
    case 'PATCH':
        if ($id === null || $id <= 0) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => "L'identifiant de la catégorie est obligatoire pour la mise à jour."
            ]);
            exit;
        }
        $controller->update($id, $body);
        break;

    case 'DELETE':
        if ($id === null || $id <= 0) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => "L'identifiant de la catégorie est obligatoire pour la suppression."
            ]);
            exit;
        }
        $controller->destroy($id);
        break;

    default:
        http_response_code(405);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => "Méthode HTTP non autorisée."
        ]);
        break;
}
