<?php

namespace App\DataFixtures;

use App\Entity\Team;
use App\Entity\Player;
use App\Repository\TeamRepository;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
    }
}
