<?php
// S3/backend/services/ValidationService.php
// Tier 2 — Validation métier
// Exceptions inline : ValidationException, DuplicateException

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategorieDAO.php';

// ── Exceptions ────────────────────────────────────────────────────────────────

class ValidationException extends RuntimeException
{
    private array $errors;
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct($errors[0] ?? 'Données invalides.');
    }
    public function getErrors(): array { return $this->errors; }
}

class DuplicateException extends RuntimeException
{
    public function __construct(string $message = 'Cette ressource existe déjà.')
    {
        parent::__construct($message);
    }
}

// ── Service ───────────────────────────────────────────────────────────────────

class ValidationService extends BaseService
{
    private CategorieDAO $dao;

    public function __construct(PDO $pdo, CategorieDAO $dao)
    {
        parent::__construct($pdo);
        $this->dao = $dao;
    }

    /**
     * Valide le nom d'une catégorie.
     * @throws ValidationException
     */
    public function validateNom(string $nom): void
    {
        $errors = [];
        if (empty($nom)) {
            $errors[] = "Le nom de la catégorie est obligatoire.";
        } elseif (mb_strlen($nom) < 2) {
            $errors[] = "Le nom de la catégorie doit comporter au moins 2 caractères.";
        } elseif (mb_strlen($nom) > 100) {
            $errors[] = "Le nom de la catégorie ne doit pas dépasser 100 caractères.";
        }
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    /**
     * Valide la description d'une catégorie (optionnelle).
     * @throws ValidationException
     */
    public function validateDescription(?string $desc): void
    {
        if ($desc !== null && mb_strlen($desc) > 255) {
            throw new ValidationException(["La description ne doit pas dépasser 255 caractères."]);
        }
    }

    /**
     * Valide les données d'un ticket.
     * @throws ValidationException
     */
    public function validateTicketData(array $data): void
    {
        $errors = [];
        $titre  = trim($data['titre'] ?? '');
        $desc   = trim($data['description'] ?? '');
        $statut = $data['statut'] ?? '';
        $idCat  = (int)($data['id_categorie'] ?? 0);
        $idUser = (int)($data['id_utilisateur'] ?? 0);

        if (empty($titre))               { $errors[] = "Le titre est obligatoire."; }
        elseif (mb_strlen($titre) < 2)   { $errors[] = "Le titre doit comporter au moins 2 caractères."; }
        elseif (mb_strlen($titre) > 200) { $errors[] = "Le titre ne doit pas dépasser 200 caractères."; }

        if (empty($desc))                { $errors[] = "La description est obligatoire."; }
        elseif (mb_strlen($desc) < 2)    { $errors[] = "La description doit comporter au moins 2 caractères."; }

        $validStatuts = ['Ouvert', 'En cours', 'Résolu', 'Fermé'];
        if (!in_array($statut, $validStatuts)) { $errors[] = "Statut invalide."; }
        if ($idCat  <= 0)                      { $errors[] = "La catégorie est obligatoire."; }
        if ($idUser <= 0)                      { $errors[] = "L'utilisateur est obligatoire."; }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    /**
     * Vérifie l'unicité du nom de catégorie.
     * @throws DuplicateException
     */
    public function checkUniqueness(string $nom, ?int $excludeId = null): void
    {
        $existing = $this->dao->findByName($nom, $excludeId);
        if ($existing !== null) {
            $msg = $excludeId !== null
                ? "Une autre catégorie portant ce nom existe déjà."
                : "Une catégorie portant ce nom existe déjà.";
            throw new DuplicateException($msg);
        }
    }
}
