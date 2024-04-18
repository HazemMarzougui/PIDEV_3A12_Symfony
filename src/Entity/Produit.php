<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Repository\ProduitRepository;
use Symfony\Component\Validator\Constraint as Assert;


#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
   

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idProduit=null;


   
    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern:"/^[^\d]+$/",
        message:"Le nom ne doit pas contenir de chiffres.")]
    #[Assert\NotBlank(message:"Le nom est obligatoire")]
    private ?string $nomProduit=null;

   
    #[ORM\Column]
    private ?int $prix=null;

   
    #[ORM\Column]
    private ?int $quantite=null;
    

    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"La description est obligatoire")]
    private ?string $description=null;

    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"L'image est obligatoire")]
    private ?string $image=null;


    
    
    #[ORM\Column]
    private ?int $idOffre=null;

  

    #[ORM\ManyToOne(targetEntity : "Categorie")]
    #[ORM\JoinColumn(name:"id_categorie", referencedColumnName:"id_categorie")]
    private ?Categorie $idCategorie=null;

    public function getIdProduit(): ?int
    {
        return $this->idProduit;
    }

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(string $nomProduit): static
    {
        $this->nomProduit = $nomProduit;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getIdOffre(): ?int
    {
        return $this->idOffre;
    }

    public function setIdOffre(int $idOffre): static
    {
        $this->idOffre = $idOffre;

        return $this;
    }

    public function getIdCategorie(): ?Categorie
    {
        return $this->idCategorie;
    }

    public function setIdCategorie(?Categorie $idCategorie): static
    {
        $this->idCategorie = $idCategorie;

        return $this;
    }

    public function __toString(): string
{
    return $this->nomProduit ?? ''; // Assuming "nomProduit" is the property containing the name of the product
}
}
