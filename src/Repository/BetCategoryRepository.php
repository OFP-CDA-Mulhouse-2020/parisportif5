<?php

namespace App\Repository;

use App\Entity\BetCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BetCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method BetCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method BetCategory[]    findAll()
 * @method BetCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BetCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BetCategory::class);
    }

    // /**
    //  * @return BetCategory[] Returns an array of BetCategory objects
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
    public function findOneBySomeField($value): ?BetCategory
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
