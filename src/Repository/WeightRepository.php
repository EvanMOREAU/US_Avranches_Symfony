<?php

namespace App\Repository;

use App\Entity\Weight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Weight>
 *
 * @method Weight|null find($id, $lockMode = null, $lockVersion = null)
 * @method Weight|null findOneBy(array $criteria, array $orderBy = null)
 * @method Weight[]    findAll()
 * @method Weight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Weight::class);
    }

    public function getLatestWeightDate(int $userId): ?\DateTimeInterface
    {
        $result = $this->createQueryBuilder('w')
            ->select('MAX(w.date) as latest_date')
            ->andWhere('w.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();

        // Si aucun poids n'est trouvé, retourner null
        if ($result === null) {
            return null;
        }

        // Convertir la chaîne de date en objet DateTimeInterface
        return new \DateTimeImmutable($result);
    }

//    /**
//     * @return Weight[] Returns an array of Weight objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Weight
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function removeByUser($user){
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'DELETE FROM tbl_weight WHERE user_id = :userId';

        $result = $conn->executeQuery($sql, ['userId' => $user->getId()]);
        return $result;
    }
}
