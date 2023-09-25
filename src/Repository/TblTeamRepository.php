<?php

namespace App\Repository;

use App\Entity\TblTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TblTeam>
 *
 * @method TblTeam|null find($id, $lockMode = null, $lockVersion = null)
 * @method TblTeam|null findOneBy(array $criteria, array $orderBy = null)
 * @method TblTeam[]    findAll()
 * @method TblTeam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TblTeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TblTeam::class);
    }

//    /**
//     * @return TblTeam[] Returns an array of TblTeam objects
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

//    public function findOneBySomeField($value): ?TblTeam
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
