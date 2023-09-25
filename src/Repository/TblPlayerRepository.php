<?php

namespace App\Repository;

use App\Entity\TblPlayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TblPlayer>
 *
 * @method TblPlayer|null find($id, $lockMode = null, $lockVersion = null)
 * @method TblPlayer|null findOneBy(array $criteria, array $orderBy = null)
 * @method TblPlayer[]    findAll()
 * @method TblPlayer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TblPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TblPlayer::class);
    }

//    /**
//     * @return TblPlayer[] Returns an array of TblPlayer objects
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

//    public function findOneBySomeField($value): ?TblPlayer
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
