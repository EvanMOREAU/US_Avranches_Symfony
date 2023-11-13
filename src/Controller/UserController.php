<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
    // #[Route('/personnal', name: 'app_profile_personnal', methods: ['GET', 'POST'])]
    // public function personnal(Request $request, ImageUploaderHelper $imageUploaderHelper,UserRepository $userRepository): Response
    //     {
    //     $user = $this->getUser(); // Récupère l'utilisateur actuellement connecté

    //     $form = $this->createForm(ProfileType::class);
    //     $form->handleRequest($request);
    //     $formView = $form->createView();

    //     if ($form->isSubmitted() && $form->isValid()) {

    //         if (!empty($errorMessage)) {
    //             $this->addFlash ('danger', 'An error has occured: '. $errorMessage);
    //         }
    //         $userRepository->save($user, true);
            
    //         return $this->render('/profile/profile.html.twig', [
    //             'connected_user' => $user,
    //             'controller_name' => 'ProfileController',
    //             'form' => $formView,
    //         ]);
    //     }

    //     return $this->render('profile/profile.html.twig', [
    //         'connected_user' => $user,
    //         'controller_name' => 'ProfileController',
    //         'form' => $formView,
    //     ]);
    // }    

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profimg = $form->get('profile_image')->getData();
            if(isset($profimg)){
                $errorMessage = $imageUploaderHelper->uploadImage($form, $user);
                if (!empty($errorMessage)) {
                    $this->addFlash ('danger', 'An error has occured: '. $errorMessage);
                }
                $userRepository->save($user, true);
           
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
