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
        $charts1->setSourceData('1,26,31');
        $manager->persist($charts1);

        $charts2 = new Charts();
        $charts2->setType('line');
        $charts2->setData('Weight');
        $charts2->setSourceData('12,26,26');
        $manager->persist($charts2);

        $charts3 = new Charts();
        $charts3->setType('line');
        $charts3->setData('LeftFoot');
        $charts3->setSourceData('30,26,29');
        $manager->persist($charts3);

        $charts4 = new Charts();
        $charts4->setType('line');
        $charts4->setData('RightFoot');
        $charts4->setSourceData('34,2,5');
        $manager->persist($charts4);

        $charts5 = new Charts();
        $charts5->setType('line');
        $charts5->setData('Head');
        $charts5->setSourceData('5,26,45');
        $manager->persist($charts5);

        $charts6 = new Charts();
        $charts6->setType('line');
        $charts6->setData('Control');
        $charts6->setSourceData('6,26,32');
        $manager->persist($charts6);

        $charts7 = new Charts();
        $charts7->setType('line');
        $charts7->setData('VMAClassic');
        $charts7->setSourceData('47,26,6');
        $manager->persist($charts7);

        $charts8 = new Charts();
        $charts8->setType('line');
        $charts8->setData('VMACooper');
        $charts8->setSourceData('28,26,40');
        $manager->persist($charts8);

        $charts9 = new Charts();
        $charts9->setType('line');
        $charts9->setData('Sprint');
        $charts9->setSourceData('19,26,15');
        $manager->persist($charts9);

        $charts10 = new Charts();
        $charts10->setType('radar');
        $charts10->setData('General');
        $charts10->setSourceData('10,26,50,25');
        $manager->persist($charts10);

        $manager->flush();
    }
}
