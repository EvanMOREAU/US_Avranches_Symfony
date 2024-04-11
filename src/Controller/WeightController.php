<?php

namespace App\Controller;

use App\Entity\Weight;
use App\Form\WeightType;
use App\Repository\WeightRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/weight')]
class WeightController extends AbstractController
{
    #[Route('/', name: 'app_weight_index', methods: ['GET'])]
    public function index(WeightRepository $weightRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        return $this->render('weight/index.html.twig', [
            'weights' => $weightRepository->findAll(),
            'location' => 'r',
        ]);
    }

    #[Route('/new', name: 'app_weight_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $weight = new Weight();
        $form = $this->createForm(WeightType::class, $weight);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $weight->setUser($user);
            $currentDate = new \DateTime();
            $weight->setDate($currentDate);
            $entityManager->persist($weight);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_default', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('weight/new.html.twig', [
            'weight' => $weight,
            'form' => $form,
            'location' => 'r',
        ]);
    }

    #[Route('/{id}', name: 'app_weight_delete', methods: ['POST'])]
    public function delete(Request $request, Weight $weight, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }
        
        if ($this->isCsrfTokenValid('delete'.$weight->getId(), $request->request->get('_token'))) {
            $entityManager->remove($weight);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_weight_index', [], Response::HTTP_SEE_OTHER);
    }
}