<?php

namespace App\Entity;

use App\Entity\Typeconseil;
use App\Entity\Produit;
use App\Repository\ConseilRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConseilRepository")
 */
class Conseil
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $idConseil;

      /**
     * @ORM\Column(length=255)
     */
    private ?string $nomConseil;

      /**
     * @ORM\Column(length=255)
     */
    private ?string $video;

      /**
     * @ORM\Column(length=255)
     */
    private ?string $description;

    /**
 * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
 */
    private ?\DateTimeInterface $datecreation;

    /**
 * @ORM\ManyToOne(targetEntity=Produit::class)
 * @ORM\JoinColumn(name="id_produit", referencedColumnName="id_produit")
 */
    private ?Produit $idProduit;

    /**
 * @ORM\ManyToOne(targetEntity=Typeconseil::class)
 * @ORM\JoinColumn(name="id_typeC", referencedColumnName="idTypeC")
 */
    private ?Typeconseil $idTypec;

    public function getIdConseil(): ?int
    {
        return $this->idConseil;
    }

    public function getNomConseil(): ?string
    {
        return $this->nomConseil;
    }

    public function setNomConseil(string $nomConseil): static
    {
        $this->nomConseil = $nomConseil;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(string $video): static
    {
        $this->video = $video;

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

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->datecreation;
    }

    public function setDatecreation(\DateTimeInterface $datecreation): static
    {
        $this->datecreation = $datecreation;

        return $this;
    }

    public function getIdProduit(): ?Produit
    {
        return $this->idProduit;
    }

    public function setIdProduit(?Produit $idProduit): static
    {
        $this->idProduit = $idProduit;

        return $this;
    }

    public function getIdTypec(): ?Typeconseil
    {
        return $this->idTypec;
    }

    public function setIdTypec(?Typeconseil $idTypec): static
    {
        $this->idTypec = $idTypec;

        return $this;
    }


}
