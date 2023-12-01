<?php

namespace App\DataFixtures;

use App\Entity\ChartConfiguration;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChartsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $heightConfig = new ChartConfiguration();
        $heightConfig->setChartType('line');
        $heightConfig->setConfigData(['entity' => 'App\Entity\Height', 'min' => 100, 'max' => 200]);
        $manager->persist($heightConfig);

        $weightConfig = new ChartConfiguration();
        $weightConfig->setChartType('line');
        $weightConfig->setConfigData(['entity' => 'App\Entity\Weight', 'min' => 100, 'max' => 200]);
        $manager->persist($weightConfig);

        $leftConfig = new ChartConfiguration();
        $leftConfig->setChartType('line');
        $leftConfig->setConfigData(['entity' => 'App\Entity\Test', 'min' => 100, 'max' => 200]);
        $manager->persist($leftConfig);

        $rightConfig = new ChartConfiguration();
        $rightConfig->setChartType('line');
        $rightConfig->setConfigData(['entity' => 'App\Entity\Height', 'min' => 100, 'max' => 200]);
        $manager->persist($rightConfig);

        $rightConfig = new ChartConfiguration();
        $rightConfig->setChartType('line');
        $rightConfig->setConfigData(['entity' => 'App\Entity\Test', 'min' => 100, 'max' => 200]);
        $manager->persist($rightConfig);

        $headConfig = new ChartConfiguration();
        $headConfig->setChartType('line');
        $headConfig->setConfigData(['entity' => 'App\Entity\Test', 'min' => 100, 'max' => 200]);
        $manager->persist($headConfig);

        $controlConfig = new ChartConfiguration();
        $controlConfig->setChartType('line');
        $controlConfig->setConfigData(['entity' => 'App\Entity\Test', 'min' => 100, 'max' => 200]);
        $manager->persist($controlConfig);

        $vmaConfig = new ChartConfiguration();
        $vmaConfig->setChartType('line');
        $vmaConfig->setConfigData(['entity' => 'App\Entity\Test', 'min' => 100, 'max' => 200]);
        $manager->persist($vmaConfig);

        $cooperConfig = new ChartConfiguration();
        $cooperConfig->setChartType('line');
        $cooperConfig->setConfigData(['entity' => 'App\Entity\Test', 'min' => 100, 'max' => 200]);
        $manager->persist($cooperConfig);;

        $demicooperConfig = new ChartConfiguration();
        $demicooperConfig->setChartType('line');
        $demicooperConfig->setConfigData(['entity' => 'App\Entity\Test', 'min' => 100, 'max' => 200]);
        $manager->persist($demicooperConfig);

        $vitesseConfig = new ChartConfiguration();
        $vitesseConfig->setChartType('line');
        $vitesseConfig->setConfigData(['entity' => 'App\Entity\Test', 'min' => 100, 'max' => 200]);
        $manager->persist($vitesseConfig);

        // $generalConfig = new ChartConfiguration();
        // $generalConfig->setChartType('radar');
        // $generalConfig->setConfigData(['entity' => 'App\Entity\Test', 'min' => 100, 'max' => 200]);
        // $manager->persist($generalConfig);

        

        $manager->flush();
    }
}