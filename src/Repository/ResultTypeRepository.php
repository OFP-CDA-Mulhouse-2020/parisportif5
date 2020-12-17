<?php

namespace App\Repository;

use App\Entity\ResultType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ResultType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResultType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResultType[]    findAll()
 * @method ResultType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResultType::class);
    }

    // /**
    //  * @return ResultType[] Returns an array of ResultType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ResultType
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
