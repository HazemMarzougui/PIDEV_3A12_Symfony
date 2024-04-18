<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EvenementRepository;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank as Assert;
use DateTime;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idEvenement=null;

    
    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern:"/^[^\d]+$/",
        message:"Le nom ne doit pas contenir de chiffres.")]
    #[Assert\NotBlank(message:"Le nom est obligatoire")]
    private ?string $nomEvent=null;

   
    #[ORM\Column(name: "date_debut", type: "date", nullable: false)]
    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    private $dateDebut;

    #[ORM\Column(name: "date_fin", type: "date", nullable: false)]
    #[Assert\NotBlank(message: "La date de fin est obligatoire.")]
    private $dateFin;

    

    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"La description est obligatoire")]
    private ?string $description=null;


    

    public function getIdEvenement(): ?int
    {
        return $this->idEvenement;
    }

    public function setIdEvenement(int $idEvenement): self
    {
        $this->idEvenement = $idEvenement;

        return $this;
    }

    public function getNomEvent(): ?string
    {
        return $this->nomEvent;
    }

    public function setNomEvent(string $nomEvent): self
    {
        $this->nomEvent = $nomEvent;

        return $this;
    }

    public function getDateDebut(): ?DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?DateTime $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    public function getDateFin(): ?DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(?DateTime $dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function __toString(): string
{
    return $this->nomEvent ?? ''; // Return the name of the event as a string
}

}
