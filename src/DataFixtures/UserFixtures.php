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
        $superAdmin->setEmail('evan.moreau@etik.com');
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

        for ($i = 0; $i < 50; $i++) {
            $randomBirthdate = $faker->dateTimeBetween('-12 years', '-9 years');
            $player = new User();
            $player->setUsername($faker->userName);
            $player->setFirstName($faker->firstName);
            $player->setLastName($faker->lastName);
            $player->setDateNaissance($randomBirthdate);
            $randomEmail = $faker->safeEmail;
            $player->setEmail($randomEmail);
            $player->setWeight(0);
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

        for ($i = 0; $i < 5; $i++) {
            $randomBirthdate = $faker->dateTimeBetween('-12 years', '-9 years');
            $coach = new User();
            $coach->setUsername($faker->userName);
            $coach->setFirstName($faker->firstName);
            $coach->setLastName($faker->lastName);
            $coach->setDateNaissance($randomBirthdate);
            $coach->setWeight(0);
            $randomEmail = $faker->safeEmail;
            $coach->setEmail($randomEmail);
            $plaintextPassword = "admin";
            $hashedPassword = $this->passwordHasher->hashPassword(
                $coach,
                $plaintextPassword
            );
            $coach->setRoles([
                "ROLE_COACH",
            ]);
            $coach->setPassword($hashedPassword);
            $manager->persist($coach);

        }
        
        $manager->flush();


    }
}