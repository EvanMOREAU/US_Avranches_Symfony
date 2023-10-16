<?php

namespace App\Repository;

use App\Entity\Charts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Charts>
 *
 * @method Charts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Charts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Charts[]    findAll()
 * @method Charts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChartsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Charts::class);
    }

//    /**
//     * @return Charts[] Returns an array of Charts objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Charts
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
