<?php

// src/DataFixtures/HeightFixtures.php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Height;
use App\Entity\User;
use Faker\Factory;

class HeightFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        // Récupérer tous les utilisateurs de la base de données
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            // Générer entre 1 et 8 hauteurs aléatoires pour chaque utilisateur
            $numberOfHeights = $faker->numberBetween(1, 8);

            for ($i = 0; $i < $numberOfHeights; $i++) {
                $height = new Height();
                $height->setValue($faker->numberBetween(150, 200)); // Exemple de valeur aléatoire entre 150 et 200 cm
                $height->setDate($faker->dateTimeBetween('-1 year', 'now'));
                $height->setUserId($user);

                $manager->persist($height);
            }
        }

        $manager->flush();
    }
}
