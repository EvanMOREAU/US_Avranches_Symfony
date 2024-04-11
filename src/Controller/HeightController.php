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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
            'location' => 'q',
        ]);
    }

    #[Route('/new', name: 'app_height_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $height = new Height();
        $form = $this->createForm(HeightType::class, $height);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $height->setUserId($user);
            $currentDate = new \DateTime();
            $height->setDate($currentDate);
            $entityManager->persist($height);
            $entityManager->flush();

            return $this->redirectToRoute('app_default', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('height/new.html.twig', [
            'height' => $height,
            'form' => $form,
            'location' => 'q',
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
