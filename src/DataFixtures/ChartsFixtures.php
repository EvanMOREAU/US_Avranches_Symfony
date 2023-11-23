<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Charts;
use App\Repository\TeamRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ChartsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $charts1 = new Charts();
        $charts1->setType('line');
        $charts1->setData('Height');
        $charts1->setSourceData('110,175,145');
        $charts1->setDatascaleMin('100');
        $charts1->setDatascaleMax('180');
        $manager->persist($charts1);

        $charts2 = new Charts();
        $charts2->setType('line');
        $charts2->setData('Weight');
        $charts2->setSourceData('62,70,41');
        $charts2->setDatascaleMin('40');
        $charts2->setDatascaleMax('80');
        $manager->persist($charts2);

        $charts3 = new Charts();
        $charts3->setType('line');
        $charts3->setData('LeftFoot');
        $charts3->setSourceData('30,26,29');
        $charts3->setDatascaleMin('0');
        $charts3->setDatascaleMax('50');
        $manager->persist($charts3);

        $charts4 = new Charts();
        $charts4->setType('line');
        $charts4->setData('RightFoot');
        $charts4->setSourceData('34,24,5');
        $charts4->setDatascaleMin('0');
        $charts4->setDatascaleMax('50');
        $manager->persist($charts4);

        $charts5 = new Charts();
        $charts5->setType('line');
        $charts5->setData('Head');
        $charts5->setSourceData('5,26,45');
        $charts5->setDatascaleMin('0');
        $charts5->setDatascaleMax('50');
        $manager->persist($charts5);

        $charts6 = new Charts();
        $charts6->setType('line');
        $charts6->setData('Control');
        $charts6->setSourceData('6,6,12');
        $charts6->setDatascaleMin('6');
        $charts6->setDatascaleMax('15');
        $manager->persist($charts6);

        $charts7 = new Charts();
        $charts7->setType('line');
        $charts7->setData('VMAClassic');
        $charts7->setSourceData('17,16,6');
        $charts7->setDatascaleMin('0');
        $charts7->setDatascaleMax('20');
        $manager->persist($charts7);

        $charts8 = new Charts();
        $charts8->setType('line');
        $charts8->setData('VMADemiCooper');
        $charts8->setSourceData('28,26,40');
        $charts8->setDatascaleMin('0');
        $charts8->setDatascaleMax('10000');
        $manager->persist($charts8);
        
        $charts9 = new Charts();
        $charts9->setType('line');
        $charts9->setData('VMACooper');
        $charts9->setSourceData('28,26,40');
        $charts9->setDatascaleMin('0');
        $charts9->setDatascaleMax('10000');
        $manager->persist($charts9);

        $charts10 = new Charts();
        $charts10->setType('line');
        $charts10->setData('Sprint');
        $charts10->setSourceData('9,6,15');
        $charts10->setDatascaleMin('6');
        $charts10->setDatascaleMax('15');
        $manager->persist($charts10);

        $charts11 = new Charts();
        $charts11->setType('radar');
        $charts11->setData('General');
        $charts11->setSourceData('10,6,5,2');
        $charts11->setDatascaleMin('0');
        $charts11->setDatascaleMax('10');
        $manager->persist($charts11);

        $manager->flush();
    }
}
