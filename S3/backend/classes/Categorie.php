<?php
// S3/backend/classes/Categorie.php - Entité Catégorie avec description

class Categorie implements JsonSerializable
{
    private ?int    $id_categorie;
    private string  $nom_categorie;
    private ?string $description;

    public function __construct(
        ?int    $id_categorie  = null,
        string  $nom_categorie = '',
        ?string $description   = null
    ) {
        $this->id_categorie  = $id_categorie;
        $this->nom_categorie = trim($nom_categorie);
        $this->description   = $description !== null ? trim($description) : null;
    }

    public function getIdCategorie(): ?int    { return $this->id_categorie; }
    public function getNomCategorie(): string  { return $this->nom_categorie; }
    public function getDescription(): ?string  { return $this->description; }

    public function setIdCategorie(?int $id): self
    {
        $this->id_categorie = $id;
        return $this;
    }

    public function setNomCategorie(string $nom): self
    {
        $this->nom_categorie = trim($nom);
        return $this;
    }

    public function setDescription(?string $desc): self
    {
        $this->description = $desc !== null ? trim($desc) : null;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id_categorie'  => $this->id_categorie,
            'nom_categorie' => $this->nom_categorie,
            'description'   => $this->description,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

