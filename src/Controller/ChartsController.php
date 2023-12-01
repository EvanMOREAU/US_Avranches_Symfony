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
    public function index(ChartsRepository $chartsRepository, ChartConfigurationRepository $configRepository, Request $request): Response {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $user = $this->getUser();
        $chartData = [];

        $charts = $chartsRepository->findAll();
        $configurations = $configRepository->findAll();

        foreach ($charts as $chart) {
            $config = $this->findConfigByType($configurations, $chart->getType());

            if ($config) {
                $chartData[$chart->getId()] = $this->getChartData($user->getId(), $config);
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

    private function getChartData($userId, $config) {
        // Utilisez les données de configuration pour construire dynamiquement la requête
        $entityClass = $config->getConfigData()['entity'];
        $minValue = $config->getConfigData()['min'];
        $maxValue = $config->getConfigData()['max'];

        return $this->getEntityManager()
            ->createQuery("
                SELECT e.value, e.date
                FROM $entityClass e
                WHERE e.user = :userId
            ")
            ->setParameter('userId', $userId)
            ->getResult();
    }

    #[Route('/new', name: 'app_charts_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chartConfiguration = new ChartConfiguration();
        $form = $this->createForm(ChartConfigurationType::class, $chartConfiguration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    public function delete(Request $request, ChartConfiguration $chartConfiguration, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chartConfiguration->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chartConfiguration);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_charts_index', [], Response::HTTP_SEE_OTHER);
    }
}
