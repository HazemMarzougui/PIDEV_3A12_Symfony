<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Utilisateur
 *
 * @ORM\Table(name="utilisateur", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_1D1C63B3E7927C74", columns={"email"})})
 * @ORM\Entity
 */
class Utilisateur
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=180, nullable=false)
     */
    private $email;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json", nullable=false)
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=50, nullable=false)
     */
    private $prenom;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tel", type="integer", nullable=true)
     */
    private $tel;

    /**
     * @var string|null
     *
     * @ORM\Column(name="adresse", type="text", length=0, nullable=true)
     */
    private $adresse;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_naiss", type="date", nullable=true)
     */
    private $dateNaiss;

    /**
     * @var string|null
     *
     * @ORM\Column(name="token", type="text", length=0, nullable=true)
     */
    private $token;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo", type="text", length=0, nullable=true)
     */
    private $photo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=0, nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_actif", type="boolean", nullable=false)
     */
    private $isActif;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=60, nullable=false)
     */
    private $role;


}
