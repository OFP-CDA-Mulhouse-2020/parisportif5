<?php

namespace App\Repository;

use App\Entity\Positive;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Positive|null find($id, $lockMode = null, $lockVersion = null)
 * @method Positive|null findOneBy(array $criteria, array $orderBy = null)
 * @method Positive[]    findAll()
 * @method Positive[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PositiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Positive::class);
    }

    // /**
    //  * @return Positive[] Returns an array of Positive objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Positive
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
