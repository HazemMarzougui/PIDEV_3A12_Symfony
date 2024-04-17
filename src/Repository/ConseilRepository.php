<?php

namespace App\Repository;

use App\Entity\Conseil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conseil>
 *
 * @method Conseil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conseil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conseil[]    findAll()
 * @method Conseil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConseilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conseil::class);
    }

//    /**
//     * @return Conseil[] Returns an array of Conseil objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Conseil
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

 /**
     * Finds the conseil by the given idd.
     *
     * @param int $idd The ID of the selected conseil
     * @return Conseil|null The conseil entity if found, null otherwise
     */
    public function findBySelectedConseilId(int $idd): ?Conseil
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :idd')
            ->setParameter('selected_conseil_id', $idd)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function conseilsCount(): int
{
    return $this->createQueryBuilder('c')
        ->select('COUNT(c.idConseil)')
        ->getQuery()
        ->getSingleScalarResult();
}

}
