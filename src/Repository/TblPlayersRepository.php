<?php

namespace App\Repository;

use App\Entity\TblPlayers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TblPlayers>
 *
 * @method TblPlayers|null find($id, $lockMode = null, $lockVersion = null)
 * @method TblPlayers|null findOneBy(array $criteria, array $orderBy = null)
 * @method TblPlayers[]    findAll()
 * @method TblPlayers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TblPlayersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TblPlayers::class);
    }

//    /**
//     * @return TblPlayers[] Returns an array of TblPlayers objects
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

//    public function findOneBySomeField($value): ?TblPlayers
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
