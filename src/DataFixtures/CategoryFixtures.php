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
        // Création de "U10"
        $category1 = new Category();
        $category1->setName("U10");
        $category1->setImage("U10.jpg");
        $category1->setColor("#AA26CF");
        $manager->persist($category1);

        // Création de "U11"
        $category2 = new Category();
        $category2->setName("U11");
        $category2->setImage("U11.jpg");
        $category2->setColor("#19B839");
        $manager->persist($category2);

        // Création de "U12"
        $category3 = new Category();
        $category3->setName("U12");
        $category3->setImage("U12.jpg");
        $category3->setColor("#D4871C");
        $manager->persist($category3);

        // Création de "U13"
        $category4 = new Category();
        $category4->setName("U13");
        $category4->setImage("U13.jpg");
        $category4->setColor("#B71515");
        $manager->persist($category4);

        // Flush (sauvegarde) tous les objets catégorie persistés dans la base de données.
        $manager->flush();
    }
}
