<?php

namespace App\Repository;

use App\Entity\Palier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Palier>
 *
 * @method Palier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Palier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Palier[]    findAll()
 * @method Palier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PalierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Palier::class);
    }
    
    public function findByNumeroLessThan($currentPalierNumber): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.numero <= :currentPalierNumber')
            ->setParameter('currentPalierNumber', $currentPalierNumber)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Palier[] Returns an array of Palier objects
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

//    public function findOneBySomeField($value): ?Palier
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
