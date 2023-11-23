<?php

namespace App\DataFixtures;

use App\Entity\PlayerCode;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;

class CodeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $code = new PlayerCode();
        $code->setCode('1234');
        $manager->persist($code);        
        $manager->flush();
    }
}