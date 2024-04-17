<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Offre
 *
 * @ORM\Table(name="offre", indexes={@ORM\Index(name="id_evenement", columns={"id_evenement_offre"}), @ORM\Index(name="offre_produit", columns={"id_produit_offre"})})
 * @ORM\Entity
 */
class Offre
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_offre", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOffre;

    /**
     * @var float
     *
     * @ORM\Column(name="montant_remise", type="float", precision=10, scale=0, nullable=false)
     */
    private $montantRemise;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="date", nullable=false)
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="date", nullable=false)
     */
    private $dateFin;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="integer", nullable=false)
     */
    private $rating;

    /**
     * @var int
     *
     * @ORM\Column(name="id_produit_offre", type="integer", nullable=false)
     */
    private $idProduitOffre;

    /**
     * @var \Evenement
     *
     * @ORM\ManyToOne(targetEntity="Evenement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_evenement_offre", referencedColumnName="id_evenement")
     * })
     */
    private $idEvenementOffre;

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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
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

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getIdProduitOffre(): ?int
    {
        return $this->idProduitOffre;
    }

    public function setIdProduitOffre(int $idProduitOffre): static
    {
        $this->idProduitOffre = $idProduitOffre;

        return $this;
    }

    public function getIdEvenementOffre(): ?Evenement
    {
        return $this->idEvenementOffre;
    }

    public function setIdEvenementOffre(?Evenement $idEvenementOffre): static
    {
        $this->idEvenementOffre = $idEvenementOffre;

        return $this;
    }


}
