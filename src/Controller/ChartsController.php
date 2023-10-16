<?php

namespace App\Controller;

use App\Entity\Charts;
use App\Form\ChartsType;
use App\Repository\ChartsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/charts')]
class ChartsController extends AbstractController {
    #[Route('/', name: 'app_charts_index', methods: ['GET'])]
    public function index(ChartsRepository $chartsRepository, Request $request): Response {
        // $chartGlobal = new Charts(); // Créez une nouvelle instance de Charts (ou adaptez ceci selon vos besoins)
        return $this->render('charts/index.html.twig', [
            'charts' => $chartsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_charts_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response {
        $chart = new Charts();
        $form = $this->createForm(ChartsType::class, $chart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chart);
            $entityManager->flush();

            return $this->redirectToRoute('app_charts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('charts/new.html.twig', [
            'chart' => $chart,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_charts_show', methods: ['GET'])]
    public function show(Charts $chart): Response {
        return $this->render('charts/show.html.twig', [
            'chart' => $chart,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_charts_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Charts $chart, EntityManagerInterface $entityManager): Response {
        $form = $this->createForm(ChartsType::class, $chart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_charts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('charts/edit.html.twig', [
            'chart' => $chart,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_charts_delete', methods: ['POST'])]
    public function delete(Request $request, Charts $chart, EntityManagerInterface $entityManager): Response {
        if ($this->isCsrfTokenValid('delete'.$chart->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_charts_index', [], Response::HTTP_SEE_OTHER);
    }
}
