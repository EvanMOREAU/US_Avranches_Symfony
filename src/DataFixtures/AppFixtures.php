<?php

namespace App\DataFixtures;

use App\Entity\TblTeam;
use App\Repository\TblTeamRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        
        $team1 = new TblTeam();
        $team1->setName('U10');
        $team1->setMatchesPlayed(0);
        $manager->persist($team1);

        $team2 = new TblTeam();
        $team2->setName('U11');
        $team2->setMatchesPlayed(0);
        $manager->persist($team2);

        $team3 = new TblTeam();
        $team3->setName('U12');
        $team3->setMatchesPlayed(0);
        $manager->persist($team3);

        $team4 = new TblTeam();
        $team4->setName('U13');
        $team4->setMatchesPlayed(0);
        $manager->persist($team4);

        $manager->flush();
    }
}
