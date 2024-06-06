<?php

namespace App\Controller;

use App\Entity\Nationality;
use App\Form\NationalityType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NationalityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/nationality')]
class NationalityController extends AbstractController
{
    #[Route('/', name: 'app_nationality_index', methods: ['GET'])]
    public function index(NationalityRepository $nationalityRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        return $this->render('nationality/index.html.twig', [
            'nationalities' => $nationalityRepository->findAll(),
            'location' => 'z',
        ]);
    }

    #[Route('/new', name: 'app_nationality_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        $nationality = new Nationality();
        $form = $this->createForm(NationalityType::class, $nationality);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($nationality);
            $entityManager->flush();

            return $this->redirectToRoute('app_nationality_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nationality/new.html.twig', [
            'nationality' => $nationality,
            'form' => $form,
            'location' => 'z',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_nationality_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Nationality $nationality, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        $form = $this->createForm(NationalityType::class, $nationality);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_nationality_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nationality/edit.html.twig', [
            'nationality' => $nationality,
            'form' => $form,
            'location' => 'z',
        ]);
    }

    #[Route('/{id}', name: 'app_nationality_delete', methods: ['POST'])]
    public function delete(Request $request, Nationality $nationality, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }
        
        if ($this->isCsrfTokenValid('delete'.$nationality->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($nationality);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nationality_index', [], Response::HTTP_SEE_OTHER);
    }
}
