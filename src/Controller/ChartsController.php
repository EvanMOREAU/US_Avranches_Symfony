<?php

namespace App\Controller;

use App\Entity\ChartConfiguration;
use App\Entity\Height;
use App\Entity\Weight;
use App\Entity\Tests;
use App\Repository\ChartConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChartsController extends AbstractController
{
    #[Route('/charts', name: 'app_charts_index', methods: ['GET'])]
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
}
