<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AuctionParticipantRepository ;
use Symfony\Component\Validator\Constraints\Date;

#[ORM\Entity(repositoryClass: AuctionParticipantRepository::class)]
class AuctionParticipant
{
    #[ORM\Column(type: "float")]
    private ?float $prix;


    #[ORM\Column(name: "date", type: "date", nullable: false)]
    private ?Date $date = null;

    #[ORM\Column(type: "integer")]
    private $love = '0';
    #[ORM\Column(type: "integer")]

    private $Ratingove = '0';


    #[ORM\OneToOne(targetEntity : "User", inversedBy : 'id_User')]
    private ?User $idParticipant;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity : "Auction", inversedBy: '$idAuction' )]
    private ?Auction $idAuction;

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getLove(): ?int
    {
        return $this->love;
    }

    public function setLove(int $love): static
    {
        $this->love = $love;

        return $this;
    }

    public function getRatingove(): ?int
    {
        return $this->Ratingove;
    }

    public function setRatingove(int $Ratingove): static
    {
        $this->Ratingove = $Ratingove;

        return $this;
    }

    public function getIdParticipant(): ?User
    {
        return $this->idParticipant;
    }

    public function setIdParticipant(?User $idParticipant): static
    {
        $this->idParticipant = $idParticipant;

        return $this;
    }

    public function getIdAuction(): ?Auction
    {
        return $this->idAuction;
    }

    public function setIdAuction(?Auction $idAuction): static
    {
        $this->idAuction = $idAuction;

        return $this;
    }

   

   


}
