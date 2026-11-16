<?php
// S3/backend/services/CategorieService.php - Tier 2 : Orchestrateur métier CRUD Catégories
// Exception inline : CategorieNotFoundException

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/ValidationService.php';
require_once __DIR__ . '/ResponseService.php';
require_once __DIR__ . '/AutoIncrementService.php';
require_once __DIR__ . '/TicketService.php';
require_once __DIR__ . '/../dao/CategorieDAO.php';
require_once __DIR__ . '/../classes/Categorie.php';

// ── Exception Métier ──────────────────────────────────────────────────────────

class CategorieNotFoundException extends RuntimeException
{
    public function __construct(string $message = 'Catégorie introuvable.')
    {
        parent::__construct($message);
    }
}

// ── Service ───────────────────────────────────────────────────────────────────

class CategorieService extends BaseService
{
    private CategorieDAO         $dao;
    private ValidationService    $validationService;
    private ResponseService      $responseService;
    private AutoIncrementService $autoIncrementService;
    private TicketService        $ticketService;

    public function __construct(
        PDO                  $pdo,
        CategorieDAO         $dao,
        ValidationService    $validationService,
        ResponseService      $responseService,
        AutoIncrementService $autoIncrementService,
        TicketService        $ticketService
    ) {
        parent::__construct($pdo);
        $this->dao                  = $dao;
        $this->validationService    = $validationService;
        $this->responseService      = $responseService;
        $this->autoIncrementService = $autoIncrementService;
        $this->ticketService        = $ticketService;
    }

    public function index(): void
    {
        try {
            $categories = $this->dao->findAllWithTicketCount();
            $this->responseService->success([
                'data'  => $categories,
                'count' => count($categories),
            ]);
        } catch (Exception $e) {
            $this->responseService->error(
                "Erreur lors de la récupération des catégories : " . $e->getMessage(), 500
            );
        }
    }

    public function show(int $id): void
    {
        if ($id <= 0) {
            $this->responseService->error("Identifiant de catégorie invalide.", 400);
            return;
        }
        try {
            $categorie = $this->dao->findById($id);
            if (!$categorie) throw new CategorieNotFoundException("Catégorie non trouvée.");
            $nbTickets = $this->ticketService->countByCategorie($id);
            $this->responseService->success([
                'data' => array_merge($categorie->toArray(), ['nb_tickets' => $nbTickets]),
            ]);
        } catch (CategorieNotFoundException $e) {
            $this->responseService->error($e->getMessage(), 404);
        } catch (Exception $e) {
            $this->responseService->error(
                "Erreur lors de la récupération : " . $e->getMessage(), 500
            );
        }
    }

    public function store(array $data): void
    {
        $nom  = trim($data['nom_categorie'] ?? '');
        $desc = (isset($data['description']) && trim($data['description']) !== '')
                ? trim($data['description'])
                : null;
        try {
            $this->validationService->validateNom($nom);
            $this->validationService->validateDescription($desc);
            $this->validationService->checkUniqueness($nom);

            $nextId    = $this->autoIncrementService->getNextId();
            $categorie = new Categorie($nextId, $nom, $desc);
            $this->dao->insert($categorie);
            $this->autoIncrementService->resetAutoIncrement($nextId + 1);

            $this->responseService->success([
                'message' => "Catégorie créée avec succès.",
                'data'    => $categorie->toArray(),
            ], 201);
        } catch (ValidationException $e) {
            $this->responseService->error($e->getMessage(), 422, ['errors' => $e->getErrors()]);
        } catch (DuplicateException $e) {
            $this->responseService->error($e->getMessage(), 409);
        } catch (Exception $e) {
            $this->responseService->error(
                "Erreur lors de la création : " . $e->getMessage(), 500
            );
        }
    }

    public function update(int $id, array $data): void
    {
        if ($id <= 0) {
            $this->responseService->error("Identifiant de catégorie invalide.", 400);
            return;
        }
        try {
            $categorie = $this->dao->findById($id);
            if (!$categorie) throw new CategorieNotFoundException("La catégorie à modifier n'existe pas.");

            $nom  = trim($data['nom_categorie'] ?? '');
            $desc = (isset($data['description']) && trim($data['description']) !== '')
                    ? trim($data['description'])
                    : null;

            $this->validationService->validateNom($nom);
            $this->validationService->validateDescription($desc);
            $this->validationService->checkUniqueness($nom, $id);

            $categorie->setNomCategorie($nom);
            $categorie->setDescription($desc);
            $this->dao->update($categorie);

            $this->responseService->success([
                'message' => "Catégorie mise à jour avec succès.",
                'data'    => $categorie->toArray(),
            ]);
        } catch (CategorieNotFoundException $e) {
            $this->responseService->error($e->getMessage(), 404);
        } catch (ValidationException $e) {
            $this->responseService->error($e->getMessage(), 422, ['errors' => $e->getErrors()]);
        } catch (DuplicateException $e) {
            $this->responseService->error($e->getMessage(), 409);
        } catch (Exception $e) {
            $this->responseService->error(
                "Erreur lors de la modification : " . $e->getMessage(), 500
            );
        }
    }

    public function destroy(int $id): void
    {
        if ($id <= 0) {
            $this->responseService->error("Identifiant de catégorie invalide.", 400);
            return;
        }
        try {
            $categorie = $this->dao->findById($id);
            if (!$categorie) throw new CategorieNotFoundException("La catégorie à supprimer n'existe pas.");

            $this->ticketService->assertNoDependencies($id);
            $this->dao->delete($id);

            $nextId = $this->autoIncrementService->getNextId();
            $this->autoIncrementService->resetAutoIncrement($nextId);

            $this->responseService->success([
                'message' => "Catégorie supprimée avec succès.",
                'id'      => $id,
            ]);
        } catch (CategorieNotFoundException $e) {
            $this->responseService->error($e->getMessage(), 404);
        } catch (HasDependenciesException $e) {
            $this->responseService->error($e->getMessage(), 409);
        } catch (Exception $e) {
            $this->responseService->error(
                "Erreur lors de la suppression : " . $e->getMessage(), 500
            );
        }
    }
}

