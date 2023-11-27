<?php

namespace App\DataFixtures;

use App\Entity\PlayerCode;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class VerifCodeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $playerCode = new PlayerCode();
        $playerCode->setCode("1234");
        
        $manager->persist($playerCode);
        $manager->flush();
    }
}