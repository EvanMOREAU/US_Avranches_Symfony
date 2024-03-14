<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CategoryFixtures extends Fixture
{
    // Cette méthode est appelée lors du chargement des fixtures et est utilisée pour peupler la base de données avec des données d'exemple.
    public function load(ObjectManager $manager): void
    {
        // Crée 4 catégories avec des noms comme "U10", "U11", "U12" et "U13".
        for ($i = 10; $i <= 13; $i++) {
            $category = new Category();

            // Définit le nom de la catégorie, par exemple, "U10".
            $category->setName("U" . $i);

            // Définit le nom de l'image pour la catégorie, par exemple, "U10.jpg".
            $category->setImage("U" . $i . ".jpg");

            // Définit le code couleur pour la catégorie, par exemple, "#FF5A5A".
            $category->setColor("#FF5A5A");

            // Persiste l'objet catégorie pour le préparer à être sauvegardé dans la base de données.
            $manager->persist($category);
        }

        // Flush (sauvegarde) tous les objets catégorie persistés dans la base de données.
        $manager->flush();
    }
}
