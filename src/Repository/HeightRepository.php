<?php

namespace App\Repository;

use App\Entity\Height;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Height>
 *
 * @method Height|null find($id, $lockMode = null, $lockVersion = null)
 * @method Height|null findOneBy(array $criteria, array $orderBy = null)
 * @method Height[]    findAll()
 * @method Height[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Height::class);
    }

//    /**
//     * @return Height[] Returns an array of Height objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Height
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function removeByUser($user){
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'DELETE FROM tbl_height WHERE user_id = :userId';

        $result = $conn->executeQuery($sql, ['userId' => $user->getId()]);
        return $result;
    }
}
