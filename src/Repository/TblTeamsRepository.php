<?php

namespace App\Repository;

use App\Entity\TblTeams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TblTeams>
 *
 * @method TblTeams|null find($id, $lockMode = null, $lockVersion = null)
 * @method TblTeams|null findOneBy(array $criteria, array $orderBy = null)
 * @method TblTeams[]    findAll()
 * @method TblTeams[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TblTeamsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TblTeams::class);
    }

//    /**
//     * @return TblTeams[] Returns an array of TblTeams objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TblTeams
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
