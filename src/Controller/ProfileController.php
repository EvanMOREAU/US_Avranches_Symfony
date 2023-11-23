<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Repository\UserRepository;
use App\Services\ImageUploaderHelper;
use Symfony\Component\Form\FormError;
use App\Service\UserVerificationService;
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

    private $passwordEncoder;
    private $userVerificationService;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, UserVerificationService $userVerificationService)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userVerificationService = $userVerificationService;
    }

    #[Route('/', name: 'app_profile_index')]
    public function index(): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
   

    #[Route('/password', name: 'app_profile_password', methods: ['GET', 'POST'])]
    public function password(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder){

        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $form = $this->createForm(PasswordType::class, $user);
        $form->handleRequest($request);
        $formView = $form->createView();

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $formView->get('password')->getData();
            $confirmPassword = $formView->get('confirmPassword')->getData();

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
