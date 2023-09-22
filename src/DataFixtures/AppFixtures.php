<?php

namespace App\DataFixtures;

use App\Entity\TblTeams;
use App\Repository\TblTeamsRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        
        $team1 = new TblTeams();
        $team1->setName('U10');
        $team1->setMatchesPlayed(0);
        $manager->persist($team1);

        $team2 = new TblTeams();
        $team2->setName('U11');
        $team2->setMatchesPlayed(0);
        $manager->persist($team2);

        $team3 = new TblTeams();
        $team3->setName('U12');
        $team3->setMatchesPlayed(0);
        $manager->persist($team3);

        $team4 = new TblTeams();
        $team4->setName('U13');
        $team4->setMatchesPlayed(0);
        $manager->persist($team4);

        $manager->flush();
    }
}
