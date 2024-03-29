<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Find users by role
     *
     * @param string $role
     * @return User[]
     */
    public function findByRole(string $role): array
    {
        $qb = $this->createQueryBuilder('u');

        $qb->andWhere(
            $qb->expr()->like('u.roles', ':role')
        );

        $qb->setParameter('role', '%' . $role . '%');

        return $qb->getQuery()->getResult();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
    
    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findUsersByPalier($palierNumero)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.palier = :palierNumero')
            ->setParameter('palierNumero', $palierNumero)
            ->getQuery()
            ->getResult();
    }

    // public function incrementMatchesPlayedForSelectedUsers(array $selectedUserIds): void
    // {
    //     $qb = $this->createQueryBuilder('user');
    //     $qb->update()
    //         ->set('user.matches_played', 'user.matches_played + 1')
    //         ->where($qb->expr()->in('user.id', $selectedUserIds))
    //         ->getQuery()
    //         ->execute();
    // }

    // public function incrementMatchesPlayedForUnselectedUsers(array $selectedUserIds, $category): void
    // {
    //     $qb = $this->createQueryBuilder('u');
    //     $qb->update()
    //         ->set('u.matches_played', 'u.matches_played + 1')
    //         ->where($qb->expr()->notIn('u.id', $selectedUserIds))
    //         ->andWhere('u.category = :category')
    //         ->setParameter('category', $category)
    //         ->getQuery()
    //         ->execute();
    // }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
