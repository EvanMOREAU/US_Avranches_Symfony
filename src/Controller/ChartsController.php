<?php
namespace App\Controller;

use App\Entity\ChartConfiguration;
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

        // Récupérer les données spécifiques de chaque joueur à partir de la table tbl_tests
        $testsData = $entityManager->getRepository(Tests::class)->findBy(['user' => $user]);

        // Parcourir les configurations de graphique
        foreach ($configurations as $config) {
            $chartData[$config->getId()] = [
                'name' => $config->getName(),
                'chartType' => $config->getChartType(),
                'data' => $this->generateLineChartData($testsData, $config->getConfigData()),
                'min' => $config->getConfigData()['min'],
                'max' => $config->getConfigData()['max'],
            ];
        }

        return $this->render('charts/index.html.twig', [
            'chartData' => $chartData,
        ]);
    }

    private function generateLineChartData($testsData, $configData)
    {
        $labels = [];
        $values = [];
    
        // Parcourir les données des tests pour générer les données du graphique en ligne
        foreach ($testsData as $test) {
            // Vérifier si le champ de la configuration est 'date'
            if ($configData['field'] === 'date') {
                // Utiliser la date de chaque test comme étiquette
                $labels[] = $test->getDate()->format('d-m-Y');
                // Utiliser les valeurs correspondantes pour les données du graphique
                $values[] = $test->{'get' . ucfirst($configData['field'])}();
            } else {
                // Utiliser le nom de la colonne spécifiée dans la configuration pour les étiquettes
                $labels[] = $test->{'get' . ucfirst($configData['date_field'])}()->format('d-m-Y');
                // Utiliser les valeurs correspondantes pour les données du graphique
                $values[] = $test->{'get' . ucfirst($configData['field'])}();
            }
        }
    
        return [
            'labels' => $labels,
            'values' => $values, // Ajouter les valeurs générées pour les données du graphique
        ];
    }
    
}
