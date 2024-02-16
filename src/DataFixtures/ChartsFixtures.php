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
        // $heightConfig = new ChartConfiguration();
        // $heightConfig->setChartType('line');
        // $heightConfig->setName('Taille');
        // $heightConfig->setConfigData(['entity' => 'App\Entity\Height', 'min' => 100, 'max' => 200, 'field' => 'heightValue']);
        // $manager->persist($heightConfig);

        // $weightConfig = new ChartConfiguration();
        // $weightConfig->setChartType('line');
        // $weightConfig->setName('Poids');
        // $weightConfig->setConfigData(['entity' => 'App\Entity\Weight', 'min' => 100, 'max' => 200, 'field' => 'weightValue']);
        // $manager->persist($weightConfig);

        // Exemple pour Jongle Gauche
        $leftConfig = new ChartConfiguration();
        $leftConfig->setChartType('line');
        $leftConfig->setName('Jongle Gauche');
        $leftConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 0, 'max' => 50, 'field' => 'jonglegauche']);
        $manager->persist($leftConfig);

        // Exemple pour Jongle Droit
        $rightConfig = new ChartConfiguration();
        $rightConfig->setChartType('line');
        $rightConfig->setName('Jongle Droit');
        $rightConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 0, 'max' => 50, 'field' => 'jongledroit']);
        $manager->persist($rightConfig);

        $headConfig = new ChartConfiguration();
        $headConfig->setChartType('line');
        $headConfig->setName('Jongle tete');
        $headConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 100, 'max' => 200, 'field' => 'jongletete']);
        $manager->persist($headConfig);

        $controlConfig = new ChartConfiguration();
        $controlConfig->setChartType('line');
        $controlConfig->setName('Controle de balle');
        $controlConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 100, 'max' => 200, 'field' => 'conduiteballe']);
        $manager->persist($controlConfig);

        $vmaConfig = new ChartConfiguration();
        $vmaConfig->setChartType('line');
        $vmaConfig->setName('VMA Classique');
        $vmaConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 100, 'max' => 200, 'field' => 'vma']);
        $manager->persist($vmaConfig);

        $cooperConfig = new ChartConfiguration();
        $cooperConfig->setChartType('line');
        $cooperConfig->setName('VMA Cooper');
        $cooperConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 100, 'max' => 200, 'field' => 'cooper']);
        $manager->persist($cooperConfig);;

        $demicooperConfig = new ChartConfiguration();
        $demicooperConfig->setChartType('line');
        $demicooperConfig->setName('VMA Demi Cooper');
        $demicooperConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 100, 'max' => 200, 'field' => 'demicooper']);
        $manager->persist($demicooperConfig);

        $vitesseConfig = new ChartConfiguration();
        $vitesseConfig->setChartType('line');
        $vitesseConfig->setName('Test Vitesse');
        $vitesseConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 100, 'max' => 200, 'field' => 'vitesse']);
        $manager->persist($vitesseConfig);

        // $generalConfig = new ChartConfiguration();
        // $generalConfig->setChartType('radar');
        // $generalConfig->setName('Test General');
        // $generalConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 100, 'max' => 200, 'field' => 'general']);
        // $manager->persist($generalConfig);

        $manager->flush();
    }
}