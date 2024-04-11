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
    public function modify(Request $request, EntityManagerInterface $entityManager, ImageUploaderHelper $imageUploaderHelper, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response // Injection de la classe Request
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $user = $this->getUser();

        $form = $this->createForm(ParameterType::class, $user);
        $form->handleRequest($request);
        // Assurez-vous que l'utilisateur est autorisé à modifier ses propres paramètres
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les valeurs des champs de mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
        
            // Assurez-vous que $plainPassword est un tableau
            if (!empty($plainPassword)) {
                // Encoder le nouveau mot de passe
                $encodedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                // Définir le mot de passe encodé dans l'entité User
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

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
        
        

        $formView = $form->createView();

        return $this->render('parameter/modify.html.twig', [
            'controller_name' => 'ParameterController',
            'form'            => $formView,
            'location' => '',
        ]);
    }
}
