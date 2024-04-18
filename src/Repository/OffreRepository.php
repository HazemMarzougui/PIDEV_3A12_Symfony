<?php

namespace App\Repository;

use App\Entity\Offre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Offre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offre[]    findAll()
 * @method Offre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offre::class);
    }

    /**
     * Get offers associated with an event by event ID.
     *
     * @param int $idEvenement The ID of the event.
     * @return array|null An array of Offer objects or null if no event found.
     */
    public function findOffersByEventId(int $idEvenement): ?array
    {
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.evenement', 'e') // Assuming 'evenement' is the property name representing the association
            ->andWhere('e.idEvenement = :idEvenement') // Adjusted to use 'idEvenement' as the identifier
            ->setParameter('idEvenement', $idEvenement);

        return $qb->getQuery()->getResult();
    }
    public function getOffersByEvent(int $id)
    {
        $entityManager = $this->getEntityManager();
        $querybuilder = $entityManager->createQueryBuilder();
        $querybuilder
                    ->select('o')
                    ->from('App\Entity\Offre' ,'o')
                    ->where('o.evenement =:idE')
                    ->setParameter('idE',$id)
                    ;
        return $querybuilder->getQuery()->getResult();
    }
}