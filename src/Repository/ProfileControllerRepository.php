<?php

namespace App\Repository;

use App\Entity\ProfileController;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProfileController>
 *
 * @method ProfileController|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfileController|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfileController[]    findAll()
 * @method ProfileController[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileControllerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfileController::class);
    }

//    /**
//     * @return ProfileController[] Returns an array of ProfileController objects
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

//    public function findOneBySomeField($value): ?ProfileController
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
