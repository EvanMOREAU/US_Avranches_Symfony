<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    
    #[Route('/personnal', name: 'app_profile_personnal')]
    public function personnal(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }





}


// #[Route('/apparence', name: 'app_account_appearance')]
//     public function image(Request $request, EntityManagerInterface $entityManager, ImageUploaderHelper $imageUploaderHelper, UserRepository $userRepository, TranslatorInterface $translator, PageRepository $pageRepository): Response
//     {
//         $this->denyAccessUnlessGranted('ROLE_USER');

//         $user = $this->getUser(); // Récupère l'utilisateur actuellement connecté

//         $form = $this->createForm(ImageType::class);
//         $form->handleRequest($request);

//         if ($form->isSubmitted() && $form->isValid()) {

//             $errorMessage = $imageUploaderHelper->uploadImage($form, $user);
//             if (!empty($errorMessage)) {
//                 $this->addFlash ('danger', $translator->trans('An error has occured: ') . $errorMessage);
//             }
//             $userRepository->save($user, true);

//             return $this->render('/account/manage.html.twig', [
//                 'connected_user' => $user,
//                 'controller_name' => 'AccountController',
//                 'form' => $form,
//                 'pages' => $pageRepository->findAll(),
//             ]);
//         }
//         return $this->render('account/image.html.twig', [
//             'connected_user' => $user,
//             'controller_name' => 'AccountController',
//             'form' => $form,
//             'pages' => $pageRepository->findAll(),
//         ]);
//     } 