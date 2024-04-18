<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Discussion
 *
 * @ORM\Table(name="discussion", indexes={@ORM\Index(name="idSender", columns={"idSender"}), @ORM\Index(name="idReciever", columns={"idReciever"})})
 * @ORM\Entity
 */
class Discussion
{
    /**
     * @var int
     *
     * @ORM\Column(name="idDis", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $iddis;

    /**
     * @var int
     *
     * @ORM\Column(name="idReciever", type="integer", nullable=false)
     */
    private $idreciever;

    /**
     * @var int
     *
     * @ORM\Column(name="idSender", type="integer", nullable=false)
     */
    private $idsender;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Sig", type="string", length=255, nullable=true)
     */
    private $sig;


}
