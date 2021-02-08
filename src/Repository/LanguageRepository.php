<?php

namespace App\Repository;

use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\Types;

/**
 * @method Language|null find($id, $lockMode = null, $lockVersion = null)
 * @method Language|null findOneBy(array $criteria, array $orderBy = null)
 * @method Language[]    findAll()
 * @method Language[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LanguageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Language::class);
    }

    public function findOneByLanguageCode(string $languageCode): ?Language
    {
        $qb = $this->createQueryBuilder('l');
        return $qb->where($qb->expr()->like('l.code', $qb->expr()->literal($languageCode . '%')))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function languageByDefault(): Language
    {
        $defaultLanguageCode = 'fr_FR';
        $defaultLanguage = $this->createQueryBuilder('l')
            ->andWhere('l.code = :code')
            ->setParameter('code', $defaultLanguageCode, Types::STRING)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (is_null($defaultLanguage)) {
            /*$defaultLanguage = $this->createQueryBuilder('l')
                ->andWhere('l.code <> :val')
                ->setParameter('val', '')
                ->orderBy('l.id', 'ASC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;*/
            $defaultLanguage = new Language();
            $defaultLanguage
                ->setName('français')
                ->setCountry('FR')
                ->setCode('fr_FR')
                ->setDateFormat('d/m/Y')
                ->setTimeFormat('H:i:s')
                ->setCapitalTimeZone('Europe/Paris');
        }

        return $defaultLanguage;
    }

    // /**
    //  * @return Language[] Returns an array of Language objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Language
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
