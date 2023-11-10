<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Repository\UserRepository;
use App\Services\ImageUploaderHelper;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/profile', name: 'app_profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile_index')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
    #[Route('/personnal', name: 'app_profile_personnal', methods: ['GET', 'POST'])]
    public function personnal(Request $request, ImageUploaderHelper $imageUploaderHelper,UserRepository $userRepository): Response
        {
        $user = $this->getUser(); // Récupère l'utilisateur actuellement connecté

        $form = $this->createForm(ProfileType::class);
        $form->handleRequest($request);
        $formView = $form->createView();

        if ($form->isSubmitted() && $form->isValid()) {

            $errorMessage = $imageUploaderHelper->uploadImage($form, $user);
            if (!empty($errorMessage)) {
                $this->addFlash ('danger', 'An error has occured: '. $errorMessage);
            }
            $userRepository->save($user, true);
            
            return $this->render('/profile/profile.html.twig', [
                'connected_user' => $user,
                'controller_name' => 'ProfileController',
                'form' => $formView,
            ]);
        }

        return $this->render('profile/profile.html.twig', [
            'connected_user' => $user,
            'controller_name' => 'ProfileController',
            'form' => $formView,
        ]);
    }    

    #[Route('/password', name: 'app_profile_password', methods: ['GET', 'POST'])]
    public function password(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder){

        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $form = $this->createForm(PasswordType::class, $user);
        $form->handleRequest($request);
        $formView = $form->createView();

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if ($password === $confirmPassword) {
                // Hash the password and set it on the user object
                $hashedPassword = $this->passwordEncoder->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

                $entityManager->flush();

                // Redirect or show a confirmation message
                return $this->render('/profile/password.html.twig', [
                    'connected_user' => $user,
                    'controller_name' => 'AccountController',
                    'form' => $formView,
                ]);
            } else {
                // Passwords do not match, you can display an error message
                $form->addError(new FormError("Passwords do not match."));
            }
        }

        return $this->render('/profile/password.html.twig', [
            'connected_user' => $user,
            'controller_name' => 'AccountController',
            'form' => $formView,
        ]);
    }
}
