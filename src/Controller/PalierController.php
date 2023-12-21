<?php

namespace App\Controller;

use App\Entity\Palier;
use App\Form\PalierType;
use App\Repository\PalierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/palier')]
class PalierController extends AbstractController
{
    #[Route('/', name: 'app_palier_index', methods: ['GET'])]
    public function index(PalierRepository $palierRepository): Response
    {
        return $this->render('palier/index.html.twig', [
            'paliers' => $palierRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_palier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $palier = new Palier();
        $form = $this->createForm(PalierType::class, $palier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($palier);
            $entityManager->flush();

            return $this->redirectToRoute('app_palier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('palier/new.html.twig', [
            'palier' => $palier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_palier_show', methods: ['GET'])]
    public function show(Palier $palier): Response
    {
        return $this->render('palier/show.html.twig', [
            'palier' => $palier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_palier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Palier $palier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PalierType::class, $palier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_palier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('palier/edit.html.twig', [
            'palier' => $palier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_palier_delete', methods: ['POST'])]
    public function delete(Request $request, Palier $palier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$palier->getId(), $request->request->get('_token'))) {
            $entityManager->remove($palier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_palier_index', [], Response::HTTP_SEE_OTHER);
    }
}
