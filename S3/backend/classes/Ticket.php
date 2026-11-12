<?php
// S3/backend/classes/Ticket.php — Entité Ticket

class Ticket implements JsonSerializable
{
    private ?int    $id_ticket;
    private string  $titre;
    private string  $description;
    private string  $statut;
    private int     $id_utilisateur;
    private int     $id_categorie;
    private ?string $date_creation;

    public static array $STATUTS = ['Ouvert', 'En cours', 'Résolu', 'Fermé'];

    public function __construct(
        ?int    $id_ticket      = null,
        string  $titre          = '',
        string  $description    = '',
        string  $statut         = 'Ouvert',
        int     $id_utilisateur = 0,
        int     $id_categorie   = 0,
        ?string $date_creation  = null
    ) {
        $this->id_ticket      = $id_ticket;
        $this->titre          = trim($titre);
        $this->description    = trim($description);
        $this->statut         = $statut;
        $this->id_utilisateur = $id_utilisateur;
        $this->id_categorie   = $id_categorie;
        $this->date_creation  = $date_creation;
    }

    public function getIdTicket(): ?int      { return $this->id_ticket; }
    public function getTitre(): string        { return $this->titre; }
    public function getDescription(): string  { return $this->description; }
    public function getStatut(): string       { return $this->statut; }
    public function getIdUtilisateur(): int   { return $this->id_utilisateur; }
    public function getIdCategorie(): int     { return $this->id_categorie; }
    public function getDateCreation(): ?string { return $this->date_creation; }

    public function setIdTicket(?int $id): self       { $this->id_ticket = $id; return $this; }
    public function setTitre(string $v): self         { $this->titre = trim($v); return $this; }
    public function setDescription(string $v): self   { $this->description = trim($v); return $this; }
    public function setStatut(string $v): self        { $this->statut = $v; return $this; }
    public function setIdUtilisateur(int $v): self    { $this->id_utilisateur = $v; return $this; }
    public function setIdCategorie(int $v): self      { $this->id_categorie = $v; return $this; }

    public function toArray(): array
    {
        return [
            'id_ticket'      => $this->id_ticket,
            'titre'          => $this->titre,
            'description'    => $this->description,
            'statut'         => $this->statut,
            'id_utilisateur' => $this->id_utilisateur,
            'id_categorie'   => $this->id_categorie,
            'date_creation'  => $this->date_creation,
        ];
    }

    public function jsonSerialize(): array { return $this->toArray(); }
}

