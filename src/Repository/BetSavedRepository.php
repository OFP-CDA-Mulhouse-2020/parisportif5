<?php

namespace App\Repository;

use App\Entity\BetSaved;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BetSaved|null find($id, $lockMode = null, $lockVersion = null)
 * @method BetSaved|null findOneBy(array $criteria, array $orderBy = null)
 * @method BetSaved[]    findAll()
 * @method BetSaved[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BetSavedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BetSaved::class);
    }

    // /**
    //  * @return BetSaved[] Returns an array of BetSaved objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BetSaved
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
