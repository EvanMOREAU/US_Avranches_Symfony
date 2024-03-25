<?php

namespace App\Controller;

use DateTime;
use App\Entity\Tests;
use App\Entity\Height;
use App\Entity\Weight;
use App\Entity\ChartConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ChartConfigurationRepository;
use App\Repository\PalierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/charts', name: 'app_charts')]
class ChartsController extends AbstractController
{
    #[Route('/details', name: 'app_charts_details', methods: ['GET'])]
    public function index(ChartConfigurationRepository $configRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Rediriger si l'utilisateur n'est pas authentifié
        if (!$user) {
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $chartData = [];

        // Récupérer toutes les configurations de graphique
        $configurations = $configRepository->findAll();

        // Parcourir les configurations de graphique
        foreach ($configurations as $config) {
            // Récupérer les données spécifiques en fonction de l'entité configurée
            $entity = $config->getConfigData()['entity'];
            $data = [];
            if ($entity === 'App\Entity\Height') {
                $data = $entityManager->getRepository(Height::class)->findBy(['user' => $user]);
            } elseif ($entity === 'App\Entity\Weight') {
                $data = $entityManager->getRepository(Weight::class)->findBy(['user' => $user]);
            } elseif ($entity === 'App\Entity\Tests') {
                $data = $entityManager->getRepository(Tests::class)->findBy(['user' => $user]);
            }

            // Générer les données du graphique
            $chartData[$config->getId()] = [
                'name' => $config->getName(),
                'chartType' => $config->getChartType(),
                'data' => $this->generateLineChartData($data, $config->getConfigData()['field']),
                'min' => $config->getConfigData()['min'],
                'max' => $config->getConfigData()['max'],
            ];
        }

        return $this->render('charts/index.html.twig', [
            'chartData' => $chartData,
            'location' => 'b',
        ]);
    }

    #[Route('/', name: 'app_charts_index', methods: ['GET'])]
    public function test(ChartConfigurationRepository $configRepository, EntityManagerInterface $entityManager, PalierRepository $palierRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $totalDataCount = $entityManager->getRepository(Height::class)->count(['user' => $user]) + 
                        $entityManager->getRepository(Weight::class)->count(['user' => $user]) + 
                        $entityManager->getRepository(Tests::class)->count(['user' => $user]);
        $currentMonth = date('m');
        $currentYear = date('Y');

        $totalDataCountThisMonth = 0;
        
        $earliestDate = null;
        $latestDate = null;
        $earliestDateWithType = null;
        $latestDateWithType = null;
        $sixLastRecord = $this->getLastSixRecordsForUser();
        foreach (['Height', 'Weight', 'Tests'] as $entityClass) {
            $entities = $entityManager->getRepository('App\\Entity\\'.$entityClass)->findBy(['user' => $user]);
            
            foreach ($entities as $entity) {
                $entityDate = $entity->getDate(); 
                if ($entityDate->format('m') == $currentMonth && $entityDate->format('Y') == $currentYear) {
                    $totalDataCountThisMonth++;
                }
                if ($earliestDate === null || $entityDate < $earliestDate) {
                    $earliestDate = $entityDate;
                    $earliestDateWithType = $entityClass;

                }
                if ($latestDate === null || $entityDate > $latestDate) {
                    $latestDate = $entityDate;
                    $latestDateWithType = $entityClass; 

                }
            }
        }
        $allowedIds = [1, 2, 7];
        $configurations = $configRepository->findById($allowedIds);
        $chartData = [];

        foreach ($configurations as $config) {
            $entity = $config->getConfigData()['entity'];
            $data = [];

            if ($entity === 'App\Entity\Height') {
                $data = $entityManager->getRepository(Height::class)->findBy(['user' => $user]);
            } elseif ($entity === 'App\Entity\Weight') {
                $data = $entityManager->getRepository(Weight::class)->findBy(['user' => $user]);
            } elseif ($entity === 'App\Entity\Tests') {
                $data = $entityManager->getRepository(Tests::class)->findBy(['user' => $user]);
            }

            $chartData[$config->getId()] = [
                'name' => $config->getName(),
                'chartType' => $config->getChartType(),
                'data' => $this->generateLineChartData($data, $config->getConfigData()['field']),
                'min' => $config->getConfigData()['min'],
                'max' => $config->getConfigData()['max'],
            ];
        }
        // dump($chartData);
        return $this->render('charts/test.html.twig', [
            'chartData' => $chartData,
            'location' => 'b',
            'totalDataCount' => $totalDataCount,
            'totalDataCountThisMonth' => $totalDataCountThisMonth,
            'earliestDate' => $earliestDate,
            'latestDate' => $latestDate,
            'earliestDateWithType' => $earliestDateWithType,
            'latestDateWithType' => $latestDateWithType,
            'paliers' => $palierRepository->findAll(),
            'sixLastRecord' => $sixLastRecord,

        ]);
    }

    private function generateLineChartData($data, $field)
    {
        $labels = [];
        $values = [];
    
        // Parcourir les données pour générer les données du graphique en ligne
        foreach ($data as $item) {
            $labels[] = $item->getDate()->format('d-m-Y');
            // Utiliser la méthode get avec le nom du champ spécifié dans la configuration
            $values[] = $item->{'get' . ucfirst($field)}();
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getLastSixRecordsForUser()
    {
        
        $user = $this->getUser();
        if (!$user) {
            return null;
        }

        $records = [];

        $weightRecords = $this->getDoctrine()->getRepository(Weight::class)->findBy(['user' => $user], ['date' => 'DESC'], 6);
        foreach ($weightRecords as $record) {
            $record->type = 'Weight';
            $records[] = $record;
        }
    
        $heightRecords = $this->getDoctrine()->getRepository(Height::class)->findBy(['user' => $user], ['date' => 'DESC'], 6);
        foreach ($heightRecords as $record) {
            $record->type = 'Height';
            $records[] = $record;
        }
    
        $testRecords = $this->getDoctrine()->getRepository(Tests::class)->findBy(['user' => $user], ['date' => 'DESC'], 6);
        foreach ($testRecords as $record) {
            $record->type = 'Tests';
            $records[] = $record;
        }
    
        usort($records, function($a, $b) {
            return $a->getDate()->getTimestamp() - $b->getDate()->getTimestamp();
        });
    
        $lastSixRecords = array_slice($records, -6);
    
        return $lastSixRecords;
    }
    
    
}
