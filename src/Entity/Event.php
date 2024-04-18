<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity
 */
class Event
{
    /**
     * @var int
     *
     * @ORM\Column(name="Id_Event", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idEvent;

    /**
     * @var string
     *
     * @ORM\Column(name="E_Name", type="string", length=255, nullable=false)
     */
    private $eName;

    /**
     * @var string
     *
     * @ORM\Column(name="Place", type="string", length=255, nullable=false)
     */
    private $place;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="E_Date", type="date", nullable=false)
     */
    private $eDate;

    /**
     * @var float
     *
     * @ORM\Column(name="Ticket_Price", type="float", precision=10, scale=0, nullable=false)
     */
    private $ticketPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=500, nullable=false)
     */
    private $image;


}
