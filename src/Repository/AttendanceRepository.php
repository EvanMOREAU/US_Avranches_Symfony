<?php

namespace App\Repository;

use App\Entity\Attendance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Attendance>
 *
 * @method Attendance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attendance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attendance[]    findAll()
 * @method Attendance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttendanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attendance::class);
    }
    /**
     * Récupère les entités Attendance associées à un utilisateur dans l'intervalle allant du 1er juillet de l'année courante au 30 juin de l'année suivante.
     *
     * @param int $userId L'ID de l'utilisateur
     * @return Attendance[] Les entités Attendance associées à l'utilisateur dans l'intervalle spécifié
     */
    public function findByUserId(int $userId): array
    {
        $currentDate = new \DateTime();
        $currentYear = (int) $currentDate->format('Y');
    
        // Calculer les dates de début et de fin
        if ($currentDate >= new \DateTime("$currentYear-07-01")) {
            $startDate = new \DateTime("$currentYear-07-01");
            $endDate = new \DateTime(($currentYear + 1) . "-06-30");
        } else {
            $startDate = new \DateTime(($currentYear - 1) . "-07-01");
            $endDate = new \DateTime("$currentYear-06-30");
        }
    
        return $this->createQueryBuilder('a')
            ->leftJoin('a.Gathering', 'g')
            ->andWhere('a.User = :userId')
            ->andWhere('g.GatheringHappenedDate BETWEEN :startDate AND :endDate')
            ->setParameter('userId', $userId)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Attendance[] Returns an array of Attendance objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Attendance
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
