<?php
// fileRouge-s2/controllers/CategorieController.php - Contrôleur API (POO)

require_once __DIR__ . '/../repositories/CategorieRepository.php';
require_once __DIR__ . '/../models/Categorie.php';

class CategorieController
{
    private CategorieRepository $repository;

    public function __construct(CategorieRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * GET /api/categories.php : Liste toutes les catégories
     */
    public function index(): void
    {
        try {
            $categories = $this->repository->findAllWithTicketCount();
            $this->jsonResponse([
                'success' => true,
                'data'    => $categories,
                'count'   => count($categories)
            ], 200);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => "Erreur lors de la récupération des catégories : " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/categories.php?id={id} : Affiche une catégorie
     */
    public function show(int $id): void
    {
        if ($id <= 0) {
            $this->jsonResponse([
                'success' => false,
                'message' => "Identifiant de catégorie invalide."
            ], 400);
            return;
        }

        try {
            $categorie = $this->repository->findById($id);

            if (!$categorie) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => "Catégorie non trouvée."
                ], 404);
                return;
            }

            $nbTickets = $this->repository->countTickets($id);

            $this->jsonResponse([
                'success' => true,
                'data'    => array_merge($categorie->toArray(), ['nb_tickets' => $nbTickets])
            ], 200);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => "Erreur lors de la récupération de la catégorie : " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/categories.php : Ajoute une catégorie
     */
    public function store(array $data): void
    {
        $nom = trim($data['nom_categorie'] ?? '');
        $categorie = new Categorie(null, $nom);

        // Validation
        $errors = $categorie->validate();
        if (!empty($errors)) {
            $this->jsonResponse([
                'success' => false,
                'message' => $errors[0],
                'errors'  => $errors
            ], 422);
            return;
        }

        try {
            // Vérification de l'unicité
            $existing = $this->repository->findByName($categorie->getNomCategorie());
            if ($existing) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => "Une catégorie portant ce nom existe déjà."
                ], 409);
                return;
            }

            $created = $this->repository->create($categorie);

            $this->jsonResponse([
                'success' => true,
                'message' => "Catégorie créée avec succès.",
                'data'    => $created->toArray()
            ], 201);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => "Erreur lors de la création de la catégorie : " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT /api/categories.php?id={id} : Modifie une catégorie
     */
    public function update(int $id, array $data): void
    {
        if ($id <= 0) {
            $this->jsonResponse([
                'success' => false,
                'message' => "Identifiant de catégorie invalide."
            ], 400);
            return;
        }

        try {
            $categorie = $this->repository->findById($id);
            if (!$categorie) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => "La catégorie à modifier n'existe pas."
                ], 404);
                return;
            }

            $nom = trim($data['nom_categorie'] ?? '');
            $categorie->setNomCategorie($nom);

            // Validation
            $errors = $categorie->validate();
            if (!empty($errors)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => $errors[0],
                    'errors'  => $errors
                ], 422);
                return;
            }

            // Vérification de l'unicité (en excluant l'ID courant)
            $existing = $this->repository->findByName($categorie->getNomCategorie(), $id);
            if ($existing) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => "Une autre catégorie portant ce nom existe déjà."
                ], 409);
                return;
            }

            $this->repository->update($categorie);

            $this->jsonResponse([
                'success' => true,
                'message' => "Catégorie mise à jour avec succès.",
                'data'    => $categorie->toArray()
            ], 200);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => "Erreur lors de la modification de la catégorie : " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/categories.php?id={id} : Supprime une catégorie
     */
    public function destroy(int $id): void
    {
        if ($id <= 0) {
            $this->jsonResponse([
                'success' => false,
                'message' => "Identifiant de catégorie invalide."
            ], 400);
            return;
        }

        try {
            $categorie = $this->repository->findById($id);
            if (!$categorie) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => "La catégorie à supprimer n'existe pas."
                ], 404);
                return;
            }

            // Vérifier s'il y a des tickets rattachés
            $nbTickets = $this->repository->countTickets($id);
            if ($nbTickets > 0) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => "Impossible de supprimer cette catégorie car {$nbTickets} ticket(s) y sont actuellement associés."
                ], 409);
                return;
            }

            $this->repository->delete($id);

            $this->jsonResponse([
                'success' => true,
                'message' => "Catégorie supprimée avec succès.",
                'id'      => $id
            ], 200);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => "Erreur lors de la suppression de la catégorie : " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envoie une réponse JSON formatée avec code HTTP
     */
    private function jsonResponse(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
