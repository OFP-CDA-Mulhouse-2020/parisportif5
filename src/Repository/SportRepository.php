<?php

namespace App\Repository;

use App\Entity\Competition;
use App\Entity\Sport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sport|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sport|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sport[]    findAll()
 * @method Sport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sport::class);
    }

    // TODO : find all with valid competitions

    /*public function findAllAttachedToCompetitionB()
    {
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('App\Entity\Sport', 's');
        $rsm->addFieldResult('s', 'id', 'id');
        $rsm->addFieldResult('s', 'name', 'name');
        $rsm->addFieldResult('s', 'country', 'country');
        $rsm->addFieldResult('s', 'run_type', 'run_type');
        $rsm->addFieldResult('s', 'individual_type', 'individual_type');
        $rsm->addFieldResult('s', 'collective_type', 'id');
        $rsm->addFieldResult('s', 'min_teams_by_run', 'min_teams_by_run');
        $rsm->addFieldResult('s', 'max_teams_by_run', 'max_teams_by_run');
        $rsm->addFieldResult('s', 'min_members_by_team', 'min_members_by_team');
        $rsm->addFieldResult('s', 'max_members_by_team', 'max_members_by_team');
        $rsm->addJoinedEntityResult('App\Entity\Competition' , 'c', 's', 'sport_id');

        $sql = 'SELECT DISTINCT s.id, s.name, s.country, s.run_type, s.individual_type, s.collective_type, s.min_teams_by_run, s.max_teams_by_run, s.min_members_by_team, s.max_members_by_team FROM sport s ' .
            'INNER JOIN competition c ON c.sport_id = s.id ORDER BY s.name ASC';
        $query = $this->_em->createNativeQuery($sql, $rsm);

        return $query->getResult();
    }*/

    /**
     * @return Sport[] Returns an array of Sport objects
    */
    public function findAllAttachedToCompetition()
    {
        /*$subquery = $this->_em->createQueryBuilder()
            ->select('c.sport_id')
            ->from('App\Entity\Competition', 'c')
            ->groupBy('c.sport_id')
            ->getDQL();
        $query = $this->_em->createQueryBuilder();
        $query->select('s')
                ->from('App\Entity\Sport', 's')
                ->where($query->expr()->in('s.id', $subquery))
                ->orderBy('s.name', 'ASC');
        $result = $query->getQuery();
        return $result->getResult();*/
        return $this->createQueryBuilder('s')
            ->groupBy('s.id')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Sport[] Returns an array of Sport objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sport
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
