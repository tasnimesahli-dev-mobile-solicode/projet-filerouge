<?php
// S3/backend/api/tickets.php
// Tier 1 — Point d'entrée HTTP + TicketController (fusionnés)

// ── CORS ──────────────────────────────────────────────────────────────────────
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// ── Dépendances ───────────────────────────────────────────────────────────────
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Ticket.php';
require_once __DIR__ . '/../classes/Categorie.php';
require_once __DIR__ . '/../dao/TicketDAO.php';
require_once __DIR__ . '/../dao/CategorieDAO.php';
require_once __DIR__ . '/../dao/UtilisateurDAO.php';
require_once __DIR__ . '/../services/BaseService.php';
require_once __DIR__ . '/../services/ValidationService.php';
require_once __DIR__ . '/../services/ResponseService.php';
require_once __DIR__ . '/../services/TicketCrudService.php';

// ── TicketController (intégré) ────────────────────────────────────────────────

class TicketController
{
    private TicketCrudService $service;

    public function __construct(TicketCrudService $service)
    {
        $this->service = $service;
    }

    public function index(): void              { $this->service->index(); }
    public function show(int $id): void        { $this->service->show($id); }
    public function store(array $data): void   { $this->service->store($data); }
    public function update(int $id, array $data): void { $this->service->update($id, $data); }
    public function destroy(int $id): void     { $this->service->destroy($id); }
}

// ── Injection des dépendances ─────────────────────────────────────────────────
$pdo               = Database::getInstance();
$ticketDAO         = new TicketDAO($pdo);
$categorieDAO      = new CategorieDAO($pdo);
$utilisateurDAO    = new UtilisateurDAO($pdo);
$validationService = new ValidationService($pdo, $categorieDAO);
$responseService   = new ResponseService($pdo);
$ticketCrudService = new TicketCrudService(
    $pdo, $ticketDAO, $categorieDAO, $utilisateurDAO, $validationService, $responseService
);
$controller = new TicketController($ticketCrudService);

// ── Méthode HTTP + override _method ──────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

// ── Corps de la requête (JSON ou Form-Data) ───────────────────────────────────
$rawBody = file_get_contents('php://input');
$body    = [];
if (!empty($rawBody)) {
    $decoded = json_decode($rawBody, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $body = $decoded;
    }
}
if (empty($body) && !empty($_POST)) { $body = $_POST; }

// ── ID ────────────────────────────────────────────────────────────────────────
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// ── Routeur RESTful ───────────────────────────────────────────────────────────
switch ($method) {
    case 'GET':
        ($id !== null && $id > 0) ? $controller->show($id) : $controller->index();
        break;

    case 'POST':
        $controller->store($body);
        break;

    case 'PUT':
    case 'PATCH':
        if ($id === null || $id <= 0) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => "L'identifiant est obligatoire pour la mise à jour."], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $controller->update($id, $body);
        break;

    case 'DELETE':
        if ($id === null || $id <= 0) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => "L'identifiant est obligatoire pour la suppression."], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $controller->destroy($id);
        break;

    default:
        http_response_code(405);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => "Méthode HTTP non autorisée."], JSON_UNESCAPED_UNICODE);
        break;
}

