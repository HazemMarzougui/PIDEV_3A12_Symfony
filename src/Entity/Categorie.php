<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
   
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idCategorie=null;

 
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"La description est obligatoire")]
    private ?string $nomCategorie=null;


}
