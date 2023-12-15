<?php

namespace App\Controller;

use App\Entity\ChartConfiguration;
use App\Form\ChartConfigurationType;
use App\Repository\ChartConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/charts')]
class ChartsController extends AbstractController
{

    #[Route('/', name: 'app_charts_index', methods: ['GET'])]
    public function index(ChartConfigurationRepository $configRepository, Request $request, EntityManagerInterface $entityManager): Response {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $user = $this->getUser();
        $chartData = [];

        $charts = $configRepository->findAll();
        $configurations = $configRepository->findAll();

        foreach ($charts as $chart) {
            $config = $this->findConfigByType($configurations, $chart->getChartType());

            if ($config) {
                $chartData[$chart->getId()] = $this->getChartData($user->getId(), $config, $entityManager);
            }
        }

        return $this->render('charts/index.html.twig', [
            'charts' => $charts,
            'chartData' => $chartData,
        ]);
    }

    private function findConfigByType($configurations, $chartType) {
        foreach ($configurations as $config) {
            if ($config->getChartType() === $chartType) {
                return $config;
            }
        }

        return null;
    }

    private function getChartData($userId, $config, EntityManagerInterface $entityManager) {
        $entityClass = $config->getConfigData()['entity'];
        $specificField = $this->getSpecificField($entityClass);
    
        $scale = $config->getConfigData()['scale'] ?? ['min' => 0, 'max' => 100];
    
        return $entityManager
            ->getRepository($entityClass)
            ->createQueryBuilder('e')
            ->select("e.$specificField as value", 'e.date')
            ->where('e.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
    
    private function getSpecificField($entityClass, $chartType) {
        $fieldMapping = [
            'App\Entity\Weight' => 'weightValue',
            'App\Entity\Height' => 'heightValue',
            'App\Entity\Tests' => [
                'line' => [
                    'jongle_droit',
                    'jongle_gauche',
                    'jongle_tete',
                    'conduiteballe',
                    'vma',
                    'cooper',
                    'demicooper',
                    'vitesse',
                ],
                // Ajoutez d'autres types de graphiques si nécessaire
            ],
        ];
    
        return $this->resolveSpecificField($entityClass, $chartType, $fieldMapping);
    }

    #[Route('/new', name: 'app_charts_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $chartConfiguration = new ChartConfiguration();
        $form = $this->createForm(ChartConfigurationType::class, $chartConfiguration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($chartConfiguration);
            $entityManager->flush();

            return $this->redirectToRoute('app_charts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('charts/new.html.twig', [
            'chart_configuration' => $chartConfiguration,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_charts_show', methods: ['GET'])]
    public function show(ChartConfiguration $chartConfiguration): Response
    {
        return $this->render('charts/show.html.twig', [
            'chart_configuration' => $chartConfiguration,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_charts_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ChartConfiguration $chartConfiguration, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChartConfigurationType::class, $chartConfiguration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_charts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('charts/edit.html.twig', [
            'chart_configuration' => $chartConfiguration,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_charts_delete', methods: ['POST'])]
    public function delete(Request $request, ChartConfiguration $chartConfiguration): Response
    {
        if ($this->isCsrfTokenValid('delete' . $chartConfiguration->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($chartConfiguration);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_charts_index', [], Response::HTTP_SEE_OTHER);
    }
}
