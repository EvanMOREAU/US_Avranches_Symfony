<?php

namespace App\DataFixtures;

use App\Entity\RegistrationA2F;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationA2FFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $playerCode = new RegistrationA2F();
        $playerCode->setCode("1234");
        
        $manager->persist($playerCode);
        $manager->flush();
    }
}