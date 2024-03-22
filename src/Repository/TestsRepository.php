<?php

namespace App\Repository;

use DateTime;
use App\Entity\Tests;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Tests>
 *
 * @method Tests|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tests|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tests[]    findAll()
 * @method Tests[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tests::class);
    }

    /**
     * Enregistre une entité Tests dans la base de données.
     *
     * @param Tests $entity L'entité à enregistrer
     * @param bool  $flush  Détermine s'il faut effectuer un flush immédiatement
     */
    public function saveEntity(Tests $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupère le dernier test créé par un utilisateur.
     *
     * @param int $userId L'ID de l'utilisateur
     *
     * @return Tests|null Le dernier test ou null s'il n'y en a pas
     */
    public function findLastTestByUser($userId)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
