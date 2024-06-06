<?php

// src/Controller/ParameterController.php
namespace App\Controller;

use App\Form\ParameterType;
use App\Repository\UserRepository;
use App\Services\ImageUploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // Ajout de cette ligne
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/parameter')]
class ParameterController extends AbstractController
{
    #[Route('/', name: 'app_parameter')]
    public function modify(Request $request, EntityManagerInterface $entityManager, ImageUploaderHelper $imageUploaderHelper, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $user = $this->getUser();

        // Determine if we are in edit mode
        $isEdit = $user->getId() !== null;

        // Initialize form options
        $formOptions = [
            'is_edit' => $isEdit,
        ];

        // Create the form with the options
        $form = $this->createForm(ParameterType::class, $user, $formOptions);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            if (!empty($plainPassword)) {
                $encodedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($encodedPassword);
            }

            $profimg = $form->get('profile_image')->getData();
            if (isset($profimg)) {
                $errorMessage = $imageUploaderHelper->uploadImage($form, $user);
                if (!empty($errorMessage)) {
                    $this->addFlash('danger', 'An error has occurred: ' . $errorMessage);
                }
                $userRepository->save($user, true);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_parameter', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parameter/modify.html.twig', [
            'controller_name' => 'ParameterController',
            'form' => $form->createView(),
            'location' => '',
        ]);
    }
}

    
