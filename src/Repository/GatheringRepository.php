<?php

namespace App\Repository;

use App\Entity\Gathering;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Gathering>
 *
 * @method Gathering|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gathering|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gathering[]    findAll()
 * @method Gathering[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GatheringRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gathering::class);
    }

//    /**
//     * @return Gathering[] Returns an array of Gathering objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Gathering
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
