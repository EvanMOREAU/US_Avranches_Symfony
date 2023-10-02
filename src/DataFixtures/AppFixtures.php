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

        $team1 = new Team();
        $team1->setName('U10');
        $team1->setMatchesPlayed(0);
        $manager->persist($team1);

        $team2 = new Team();
        $team2->setName('U11');
        $team2->setMatchesPlayed(0);
        $manager->persist($team2);

        $team3 = new Team();
        $team3->setName('U12');
        $team3->setMatchesPlayed(0);
        $manager->persist($team3);

        $team4 = new Team();
        $team4->setName('U13');
        $team4->setMatchesPlayed(0);
        $manager->persist($team4);

        // Create 8 players
        for ($i = 1; $i <= 8; $i++) {
            $player = new Player();
            $player->setTeam($team1);
            $player->setFirstName('Arthur');
            $player->setLastName('Delacour');
            $player->setBirthdate(new DateTime('2013-01-04'));
            $player->setMatchesPlayed(0);
            $manager->persist($player);
        }

        $manager->flush();
    }
}
