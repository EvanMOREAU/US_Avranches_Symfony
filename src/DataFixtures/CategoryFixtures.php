<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create 4 categories 
        for ($i = 10; $i <= 13; $i++) {
            $category = new Category();
            $category->setName("U" . $i);
            $category->setImage('8===D');
            $manager->persist($category);
        }

        $manager->flush();
    }
}