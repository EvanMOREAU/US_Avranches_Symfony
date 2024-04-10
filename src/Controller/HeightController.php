<?php

namespace App\Controller;

use App\Entity\Height;
use App\Form\HeightType;
use App\Repository\HeightRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/height')]
class HeightController extends AbstractController
{
    #[Route('/', name: 'app_height_index', methods: ['GET'])]
    public function index(HeightRepository $heightRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        return $this->render('height/index.html.twig', [
            'heights' => $heightRepository->findAll(),
            'location' => '',
        ]);
    }

    #[Route('/new', name: 'app_height_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $height = new Height();
        $form = $this->createForm(HeightType::class, $height);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = $this->get('security.token_storage')->getToken();
            $user = $token->getUser();
            $height->setUserId($user);
            $currentDate = new \DateTime();
            $height->setDate($currentDate);
            $entityManager->persist($height);
            $entityManager->flush();

            return $this->redirectToRoute('app_default', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('height/new.html.twig', [
            'height' => $height,
            'form' => $form,
            'location' => '',
        ]);
    }

    #[Route('/{id}', name: 'app_height_show', methods: ['GET'])]
    public function show(Height $height): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        return $this->render('height/show.html.twig', [
            'height' => $height,
            'location' => '',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_height_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Height $height, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        $form = $this->createForm(HeightType::class, $height);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_height_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('height/edit.html.twig', [
            'height' => $height,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_height_delete', methods: ['POST'])]
    public function delete(Request $request, Height $height, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }
        
        if ($this->isCsrfTokenValid('delete'.$height->getId(), $request->request->get('_token'))) {
            $entityManager->remove($height);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_height_index', [], Response::HTTP_SEE_OTHER);
    }
}
