<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Conseil
 *
 * @ORM\Table(name="conseil", indexes={@ORM\Index(name="id_typeC", columns={"id_typeC"}), @ORM\Index(name="conseil_produit", columns={"id_produit"})})
 * @ORM\Entity
 */
class Conseil
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_conseil", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idConseil;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_conseil", type="string", length=255, nullable=false)
     */
    private $nomConseil;

    /**
     * @var string
     *
     * @ORM\Column(name="video", type="string", length=255, nullable=false)
     */
    private $video;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $datecreation = 'CURRENT_TIMESTAMP';

    /**
     * @var \Typeconseil
     *
     * @ORM\ManyToOne(targetEntity="Typeconseil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_typeC", referencedColumnName="idTypeC")
     * })
     */
    private $idTypec;

    /**
     * @var \Produit
     *
     * @ORM\ManyToOne(targetEntity="Produit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_produit", referencedColumnName="id_produit")
     * })
     */
    private $idProduit;


}
