<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Workshop
 *
 * @ORM\Table(name="workshop", indexes={@ORM\Index(name="Id_Event", columns={"Id_Event"})})
 * @ORM\Entity
 */
class Workshop
{
    /**
     * @var int
     *
     * @ORM\Column(name="Id_Workshop", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idWorkshop;

    /**
     * @var string
     *
     * @ORM\Column(name="Title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="Details", type="string", length=255, nullable=false)
     */
    private $details;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=500, nullable=false)
     */
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(name="Id_Event", type="integer", nullable=false)
     */
    private $idEvent;


}
