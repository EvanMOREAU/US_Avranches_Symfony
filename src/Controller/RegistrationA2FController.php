<?php

namespace App\Controller;

use App\Entity\RegistrationA2F;
use App\Form\RegistrationA2FType;
use App\Repository\RegistrationA2FRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/registration-code')]
class RegistrationA2FController extends AbstractController
{
    #[Route('/', name: 'app_registration_a2f_index', methods: ['GET'])]
    public function index(RegistrationA2FRepository $registrationA2FRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        return $this->render('registration_a2f/index.html.twig', [
            'registration_a2fs' => $registrationA2FRepository->findAll(),
            'location' => 'x',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_registration_a2f_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RegistrationA2F $registrationA2F, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }
        
        $form = $this->createForm(RegistrationA2FType::class, $registrationA2F);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_registration_a2f_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('registration_a2f/edit.html.twig', [
            'registration_a2f' => $registrationA2F,
            'form' => $form,
            'location' => 'x',
        ]);
    }
}
