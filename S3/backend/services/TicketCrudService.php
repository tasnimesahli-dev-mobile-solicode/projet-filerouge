<?php
// S3/backend/services/TicketCrudService.php
// Tier 2 — CRUD complet des tickets
// Exception inline : TicketNotFoundException

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/ValidationService.php';
require_once __DIR__ . '/ResponseService.php';
require_once __DIR__ . '/../dao/TicketDAO.php';
require_once __DIR__ . '/../dao/CategorieDAO.php';
require_once __DIR__ . '/../dao/UtilisateurDAO.php';
require_once __DIR__ . '/../classes/Ticket.php';

// ── Exception ─────────────────────────────────────────────────────────────────

class TicketNotFoundException extends RuntimeException
{
    public function __construct(string $message = 'Ticket introuvable.')
    {
        parent::__construct($message);
    }
}

// ── Service ───────────────────────────────────────────────────────────────────

class TicketCrudService extends BaseService
{
    private TicketDAO         $ticketDAO;
    private CategorieDAO      $categorieDAO;
    private UtilisateurDAO    $utilisateurDAO;
    private ValidationService $validationService;
    private ResponseService   $responseService;

    public function __construct(
        PDO               $pdo,
        TicketDAO         $ticketDAO,
        CategorieDAO      $categorieDAO,
        UtilisateurDAO    $utilisateurDAO,
        ValidationService $validationService,
        ResponseService   $responseService
    ) {
        parent::__construct($pdo);
        $this->ticketDAO         = $ticketDAO;
        $this->categorieDAO      = $categorieDAO;
        $this->utilisateurDAO    = $utilisateurDAO;
        $this->validationService = $validationService;
        $this->responseService   = $responseService;
    }

    // ── GET — Liste ───────────────────────────────────────────────────────────
    public function index(): void
    {
        try {
            $tickets = $this->ticketDAO->findAllWithDetails();
            $this->responseService->success([
                'data'  => $tickets,
                'count' => count($tickets),
            ]);
        } catch (Exception $e) {
            $this->responseService->error(
                "Erreur lors de la récupération des tickets : " . $e->getMessage(), 500
            );
        }
    }

    // ── GET ?id — Détail ──────────────────────────────────────────────────────
    public function show(int $id): void
    {
        if ($id <= 0) {
            $this->responseService->error("Identifiant de ticket invalide.", 400);
            return;
        }
        try {
            $ticket = $this->ticketDAO->findByIdWithDetails($id);
            if (!$ticket) throw new TicketNotFoundException("Ticket non trouvé.");
            $this->responseService->success(['data' => $ticket]);
        } catch (TicketNotFoundException $e) {
            $this->responseService->error($e->getMessage(), 404);
        } catch (Exception $e) {
            $this->responseService->error(
                "Erreur lors de la récupération : " . $e->getMessage(), 500
            );
        }
    }

    // ── POST — Création ───────────────────────────────────────────────────────
    public function store(array $data): void
    {
        try {
            $this->validationService->validateTicketData($data);

            $idCat  = (int)($data['id_categorie']   ?? 0);
            $idUser = (int)($data['id_utilisateur'] ?? 0);

            if (!$this->categorieDAO->findById($idCat)) {
                $this->responseService->error("La catégorie sélectionnée n'existe pas.", 422);
                return;
            }
            if (!$this->utilisateurDAO->existsById($idUser)) {
                $this->responseService->error("L'utilisateur sélectionné n'existe pas.", 422);
                return;
            }

            $ticket = new Ticket(
                null,
                trim($data['titre']),
                trim($data['description']),
                $data['statut'],
                $idUser,
                $idCat
            );
            $this->ticketDAO->insert($ticket);

            $this->responseService->success([
                'message' => "Ticket créé avec succès.",
                'data'    => $ticket->toArray(),
            ], 201);
        } catch (ValidationException $e) {
            $this->responseService->error($e->getMessage(), 422, ['errors' => $e->getErrors()]);
        } catch (Exception $e) {
            $this->responseService->error(
                "Erreur lors de la création : " . $e->getMessage(), 500
            );
        }
    }

    // ── PUT — Modification ────────────────────────────────────────────────────
    public function update(int $id, array $data): void
    {
        if ($id <= 0) {
            $this->responseService->error("Identifiant de ticket invalide.", 400);
            return;
        }
        try {
            $ticket = $this->ticketDAO->findById($id);
            if (!$ticket) throw new TicketNotFoundException("Le ticket à modifier n'existe pas.");

            $this->validationService->validateTicketData($data);

            $idCat  = (int)($data['id_categorie']   ?? 0);
            $idUser = (int)($data['id_utilisateur'] ?? 0);

            if (!$this->categorieDAO->findById($idCat)) {
                $this->responseService->error("La catégorie sélectionnée n'existe pas.", 422);
                return;
            }
            if (!$this->utilisateurDAO->existsById($idUser)) {
                $this->responseService->error("L'utilisateur sélectionné n'existe pas.", 422);
                return;
            }

            $ticket->setTitre(trim($data['titre']))
                   ->setDescription(trim($data['description']))
                   ->setStatut($data['statut'])
                   ->setIdUtilisateur($idUser)
                   ->setIdCategorie($idCat);

            $this->ticketDAO->update($ticket);

            $this->responseService->success([
                'message' => "Ticket mis à jour avec succès.",
                'data'    => $ticket->toArray(),
            ]);
        } catch (TicketNotFoundException $e) {
            $this->responseService->error($e->getMessage(), 404);
        } catch (ValidationException $e) {
            $this->responseService->error($e->getMessage(), 422, ['errors' => $e->getErrors()]);
        } catch (Exception $e) {
            $this->responseService->error(
                "Erreur lors de la modification : " . $e->getMessage(), 500
            );
        }
    }

    // ── DELETE — Suppression ──────────────────────────────────────────────────
    public function destroy(int $id): void
    {
        if ($id <= 0) {
            $this->responseService->error("Identifiant de ticket invalide.", 400);
            return;
        }
        try {
            $ticket = $this->ticketDAO->findById($id);
            if (!$ticket) throw new TicketNotFoundException("Le ticket à supprimer n'existe pas.");

            $this->ticketDAO->delete($id);

            $this->responseService->success([
                'message' => "Ticket supprimé avec succès.",
                'id'      => $id,
            ]);
        } catch (TicketNotFoundException $e) {
            $this->responseService->error($e->getMessage(), 404);
        } catch (Exception $e) {
            $this->responseService->error(
                "Erreur lors de la suppression : " . $e->getMessage(), 500
            );
        }
    }
}

