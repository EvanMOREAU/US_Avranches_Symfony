<?php
// src/DataFixtures/WeightFixtures.php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Weight;
use App\Entity\User;
use Faker\Factory;

class WeightFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        // Récupérer tous les utilisateurs de la base de données
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            // Générer entre 1 et 8 poids aléatoires pour chaque utilisateur
            $numberOfWeights = $faker->numberBetween(1, 8);

            for ($i = 0; $i < $numberOfWeights; $i++) {
                $weight = new Weight();
                $weight->setValue($faker->randomFloat(2, 50, 100)); // Exemple de valeur aléatoire entre 50 et 100
                $weight->setDate($faker->dateTimeBetween('-1 year', 'now'));
                $weight->setUserId($user);

                $manager->persist($weight);
            }
        }

        $manager->flush();
    }
}
