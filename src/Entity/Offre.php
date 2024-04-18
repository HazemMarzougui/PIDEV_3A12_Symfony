<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OffreRepository;

#[ORM\Entity(repositoryClass: OffreRepository::class)]
class Offre
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idOffre=null;

    

    #[ORM\Column]
    private ?float $montantRemise = null;



    #[ORM\Column(name: "date_debut", type: "date", nullable: false)]
    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    private $dateDebut;



    #[ORM\Column(name: "date_fin", type: "date", nullable: false)]
    #[Assert\NotBlank(message: "La date de fin est obligatoire.")]
    private $dateFin;



    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"La description est obligatoire")]
    private ?string $description=null;

    

    #[ORM\ManyToOne(targetEntity : "Evenement")]
    #[ORM\JoinColumn(name:"id_evenement_offre", referencedColumnName:"id_evenement")]
    private ?Evenement $evenement=null;
    


    #[ORM\ManyToOne(targetEntity : "Produit")]
    #[ORM\JoinColumn(name:"id_produit_offre", referencedColumnName:"id_produit")]
    private ?Produit $idProduitOffre=null;

    public function getIdOffre(): ?int
    {
        return $this->idOffre;
    }

    public function getMontantRemise(): ?float
    {
        return $this->montantRemise;
    }

    public function setMontantRemise(float $montantRemise): static
    {
        $this->montantRemise = $montantRemise;

        return $this;
    }

    public function getDateDebut(): ?DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?DateTime  $dateDebut): void
    {
        $this->dateDebut = $dateDebut;

    }

    public function getDateFin(): ?DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(?DateTime  $dateFin): void
    {
        $this->dateFin = $dateFin;

        
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): static
    {
        $this->evenement = $evenement;

        return $this;
    }

    public function getIdProduitOffre(): ?Produit
    {
        return $this->idProduitOffre;
    }

    public function setIdProduitOffre(?Produit $idProduitOffre): static
    {
        $this->idProduitOffre = $idProduitOffre;

        return $this;
    }

}
