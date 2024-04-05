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
        $heightConfig->setName('Taille');
        $heightConfig->setPremierPalier('130');
        $heightConfig->setDeuxiemePalier('160');
        $heightConfig->setConfigData(['entity' => 'App\Entity\Height', 'min' => 100, 'max' => 200, 'field' => 'value', 'date_field' => 'date']);
        $manager->persist($heightConfig);

        $weightConfig = new ChartConfiguration();
        $weightConfig->setChartType('line');
        $weightConfig->setName('Poids');
        $heightConfig->setPremierPalier('30');
        $heightConfig->setDeuxiemePalier('80');
        $weightConfig->setConfigData(['entity' => 'App\Entity\Weight', 'min' => 10, 'max' => 120, 'field' => 'value', 'date_field' => 'date']);
        $manager->persist($weightConfig);

        
        $leftConfig = new ChartConfiguration();
        $leftConfig->setChartType('line');
        $leftConfig->setName('Jongle Gauche');
        $heightConfig->setPremierPalier('16');
        $heightConfig->setDeuxiemePalier('33');
        $leftConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 0, 'max' => 50, 'field' => 'jonglegauche', 'date_field' => 'date']);
        $manager->persist($leftConfig);

    
        $rightConfig = new ChartConfiguration();
        $rightConfig->setChartType('line');
        $rightConfig->setName('Jongle Droit');
        $heightConfig->setPremierPalier('16');
        $heightConfig->setDeuxiemePalier('33');
        $rightConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 0, 'max' => 50, 'field' => 'jongledroit', 'date_field' => 'date']);
        $manager->persist($rightConfig);

        $headConfig = new ChartConfiguration();
        $headConfig->setChartType('line');
        $headConfig->setName('Jongle tete');
        $heightConfig->setPremierPalier('10');
        $heightConfig->setDeuxiemePalier('20');
        $headConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 0, 'max' => 30, 'field' => 'jongletete', 'date_field' => 'date']);
        $manager->persist($headConfig);

        $controlConfig = new ChartConfiguration();
        $controlConfig->setChartType('line');
        $controlConfig->setName('Controle de balle');
        $heightConfig->setPremierPalier('10');
        $heightConfig->setDeuxiemePalier('15');
        $controlConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 5, 'max' => 20, 'field' => 'conduiteballe', 'date_field' => 'date']);
        $manager->persist($controlConfig);

        $vmaConfig = new ChartConfiguration();
        $vmaConfig->setChartType('line');
        $vmaConfig->setName('VMA Classique');
        $heightConfig->setPremierPalier('6');
        $heightConfig->setDeuxiemePalier('13');
        $vmaConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 0, 'max' => 20, 'field' => 'vma', 'date_field' => 'date']);
        $manager->persist($vmaConfig);

        $cooperConfig = new ChartConfiguration();
        $cooperConfig->setChartType('line');
        $cooperConfig->setName('VMA Cooper');
        $heightConfig->setPremierPalier('6');
        $heightConfig->setDeuxiemePalier('13');
        $cooperConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 0, 'max' => 20, 'field' => 'cooper', 'date_field' => 'date']);
        $manager->persist($cooperConfig);;

        $demicooperConfig = new ChartConfiguration();
        $demicooperConfig->setChartType('line');
        $demicooperConfig->setName('VMA Demi Cooper');
        $heightConfig->setPremierPalier('6');
        $heightConfig->setDeuxiemePalier('13');
        $demicooperConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 0, 'max' => 20, 'field' => 'demicooper', 'date_field' => 'date']);
        $manager->persist($demicooperConfig);

        $vitesseConfig = new ChartConfiguration();
        $vitesseConfig->setChartType('line');
        $vitesseConfig->setName('Test Vitesse');
        $heightConfig->setPremierPalier('130');
        $heightConfig->setDeuxiemePalier('160');
        $vitesseConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 5, 'max' => 20, 'field' => 'vitesse', 'date_field' => 'date']);
        $manager->persist($vitesseConfig);

        // $generalConfig = new ChartConfiguration();
        // $generalConfig->setChartType('radar');
        // $generalConfig->setName('Test General');
        // $generalConfig->setConfigData(['entity' => 'App\Entity\Tests', 'min' => 100, 'max' => 200, 'field' => 'general', 'date_field' => 'date']);
        // $manager->persist($generalConfig);

        $manager->flush();
    }
}