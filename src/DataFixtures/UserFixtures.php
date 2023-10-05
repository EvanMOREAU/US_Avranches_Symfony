<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        /* Utilisateur Dev */
        $dateNaissance = new \DateTime('2004-12-27');
        $superAdmin = new User();
        $superAdmin->setUsername('adminSIO');
        $superAdmin->setFirstName('SIO');
        $superAdmin->setLastName('2');
        $superAdmin->setDateNaissance($dateNaissance);
        $plaintextPassword = "admin";
        $hashedPassword = $this->passwordHasher->hashPassword(
            $superAdmin,
            $plaintextPassword
        );
        $superAdmin->setPassword($hashedPassword);
        $superAdmin->setRoles([
            "ROLE_SUPER_ADMIN",
        ]);
        $manager->persist($superAdmin);


        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 150; $i++) {
            $randomBirthdate = $faker->dateTimeBetween('-10 years', '-6 years');
            $player = new User();
            $player->setUsername($faker->userName);
            $player->setFirstName($faker->firstName);
            $player->setLastName($faker->lastName);
            $player->setDateNaissance($randomBirthdate);
            $plaintextPassword = "admin";
            $hashedPassword = $this->passwordHasher->hashPassword(
                $player,
                $plaintextPassword
            );
            $player->setRoles([
                "ROLE_PLAYER",
            ]);
            $player->setPassword($hashedPassword);
            $manager->persist($player);

        }
        
        $manager->flush();


    }
}