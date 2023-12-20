<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use App\Services\ImageUploaderHelper;
use App\Service\UserVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{

    private $userVerificationService;

    public function __construct(UserVerificationService $userVerificationService)
    {
        $this->userVerificationService = $userVerificationService;
    }
  
    #[Route('/poste/set-poste-principal/{id}', name: 'app_set_poste_principal')]
    public function setPostePrincipal(user $user, Request $request, LoggerInterface $logger): Response
    {
        $logger->debug('setPostePrincipal() user->getFirstname() = ' . $user->getFirstname());

        // Récupérez les données de la requête AJAX
        $postePrincipal = $_POST["postePrincipal"];
        $logger->debug('setPostePrincipal() postePrincipal = ' . $postePrincipal);

        // Mettez à jour l'entité user
        $user->setPostePrincipal($postePrincipal);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
    }

    #[Route('/poste/set-poste-secondaire/{id}', name: 'app_set_poste_secondaire')]
    public function setPosteSecondaire(user $user, Request $request, LoggerInterface $logger): Response
    {
        $logger->debug('setPosteSecondaire() user->getFirstname() = ' . $user->getFirstname());

        // Récupérez les données de la requête AJAX
        $posteSecondaire = $_POST["posteSecondaire"];
        $logger->debug('setPosteSecondaire() posteSecondaire = ' . $posteSecondaire);

        // Mettez à jour l'entité user
        $user->setPosteSecondaire($posteSecondaire);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
    }
   

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

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
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository, ImageUploaderHelper $imageUploaderHelper, UserPasswordHasherInterface $passwordHasher): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isGranted('ROLE_PLAYER')) {
            $form = $this->createForm(UserType::class, $user, ['exclude_date_naissance' => true]);
        } else {
            $form = $this->createForm(UserType::class, $user);
        }
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la valeur du champ de mot de passe
            $plainPassword = $form->get('plainPassword')->getData();

            // Vérifier si le mot de passe n'est pas vide (indiquant un changement de mot de passe)
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

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/poste', name: 'app_user_poste', methods: ['GET'])]
    public function poste(user $user, LoggerInterface $logger): Response
    {
        // $logger->debug('poste() user->getFirstname() = ' . $user->getFirstname());
        return $this->render('user/poste.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/poste-cache', name: 'app_user_cacheposte', methods: ['GET'])]
    public function poste_cache(user $user, LoggerInterface $logger): Response
    {
        // $logger->debug('poste() user->getFirstname() = ' . $user->getFirstname());
        return $this->render('user/hiddenposte.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/poste/poste-coach', name: 'app_user_coach', methods: ['GET'])]
    public function poste_coach(LoggerInterface $logger): Response
    {
        $users = $this->getDoctrine()->getRepository(user::class)->findAll();

        return $this->render('user/coachposte.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/poste/set-poste-cache-x/{id}', name: 'app_set_poste_cache_x')]
    public function setPosteCacheX(user $user, Request $request, LoggerInterface $logger): Response
    {
        $logger->debug('setPosteCacheX() user->getFirstname() = ' . $user->getFirstname());

        // Récupérez les données de la requête AJAX
        $coord = $_POST["coordX"];
        $logger->debug('setPosteCoordX() PosteCoordX = ' . $coord);

        // Mettez à jour l'entité user
        $user->setPosteCoordX($coord);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
    }

    #[Route('/poste/set-poste-cache-y/{id}', name: 'app_set_poste_cache_y')]
    public function setPosteCacheY(user $user, Request $request, LoggerInterface $logger): Response
    {
        $logger->debug('setPosteCacheY() user->getFirstname() = ' . $user->getFirstname());

        // Récupérez les données de la requête AJAX
        $coord = $_POST["coordY"];
        $logger->debug('setPosteCoordY() PosteCoordY = ' . $coord);

        // Mettez à jour l'entité user
        $user->setPosteCordY($coord);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
    }

    //////////////////////////////
}
