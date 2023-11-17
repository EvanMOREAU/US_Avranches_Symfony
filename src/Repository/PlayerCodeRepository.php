<?php

namespace App\Repository;

use App\Entity\PlayerCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerCode>
 *
 * @method PlayerCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerCode[]    findAll()
 * @method PlayerCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerCode::class);
    }

//    /**
//     * @return PlayerCode[] Returns an array of PlayerCode objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PlayerCode
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
