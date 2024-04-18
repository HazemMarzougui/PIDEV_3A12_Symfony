<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Review
 *
 * @ORM\Table(name="review", indexes={@ORM\Index(name="id_conseil", columns={"id_conseil"})})
 * @ORM\Entity
 */
class Review
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_review", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idReview;

    /**
     * @var string
     *
     * @ORM\Column(name="titile", type="string", length=255, nullable=false)
     */
    private $titile;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="string", length=255, nullable=false)
     */
    private $comments;

    /**
     * @var int
     *
     * @ORM\Column(name="value", type="integer", nullable=false)
     */
    private $value;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateCreation", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $datecreation = 'CURRENT_TIMESTAMP';

    /**
     * @var \Conseil
     *
     * @ORM\ManyToOne(targetEntity="Conseil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_conseil", referencedColumnName="id_conseil")
     * })
     */
    private $idConseil;


}
