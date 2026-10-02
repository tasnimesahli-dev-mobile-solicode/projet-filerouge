<?php
// fileRouge-s2/models/Categorie.php - Modèle POO pour l'entité Catégorie

class Categorie implements JsonSerializable
{
    private ?int $id_categorie;
    private string $nom_categorie;

    public function __construct(?int $id_categorie = null, string $nom_categorie = '')
    {
        $this->id_categorie = $id_categorie;
        $this->nom_categorie = trim($nom_categorie);
    }

    public function getIdCategorie(): ?int
    {
        return $this->id_categorie;
    }

    public function setIdCategorie(?int $id_categorie): self
    {
        $this->id_categorie = $id_categorie;
        return $this;
    }

    public function getNomCategorie(): string
    {
        return $this->nom_categorie;
    }

    public function setNomCategorie(string $nom_categorie): self
    {
        $this->nom_categorie = trim($nom_categorie);
        return $this;
    }

    /**
     * Valide les données de la catégorie
     * @return array Tableau des erreurs éventuelles
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->nom_categorie)) {
            $errors[] = "Le nom de la catégorie est obligatoire.";
        } elseif (mb_strlen($this->nom_categorie) < 2) {
            $errors[] = "Le nom de la catégorie doit comporter au moins 2 caractères.";
        } elseif (mb_strlen($this->nom_categorie) > 100) {
            $errors[] = "Le nom de la catégorie ne doit pas dépasser 100 caractères.";
        }

        return $errors;
    }

    public function toArray(): array
    {
        return [
            'id_categorie'  => $this->id_categorie,
            'nom_categorie' => $this->nom_categorie,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
