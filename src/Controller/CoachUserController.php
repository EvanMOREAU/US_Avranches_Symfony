<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User1Type;
use App\Repository\UserRepository;
use App\Repository\PalierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/coach-account')]
class CoachUserController extends AbstractController
{
    #[Route('/', name: 'app_coach_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        // Récupérer tous les utilisateurs
        $users = $userRepository->findAll();
    
        // Filtrer les utilisateurs ayant le rôle "ROLE_COACH"
        $coachUsers = array_filter($users, function($user) {
            return in_array('ROLE_COACH', $user->getRoles());
        });
    
        return $this->render('coach_user/index.html.twig', [
            'users' => $coachUsers,
            'location' => 'p',
        ]);
    }

    #[Route('/new', name: 'app_coach_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, PalierRepository $palierRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        $user = new User();
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(["ROLE_COACH"]);
            $user->setIsCodeValidated(true);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            
            $latestPalier = $palierRepository->findOneBy([], ['id' => 'DESC']);
            if ($latestPalier) {
                $user->setPalier($latestPalier);
            }
    
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_coach_user_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('coach_user/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'location' => 'p',
        ]);
    }
    #[Route('/{id}', name: 'app_coach_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }
        
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            // Si non, vérifie si l'utilisateur a le rôle ROLE_COACH
            if (!$this->isGranted('ROLE_COACH')) {
                // Si l'utilisateur n'a aucun rôle, refuser l'accès
                throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
            }
        }
        
        // Vérifie le jeton CSRF et supprime la catégorie
            $entityManager->remove($user);
            $entityManager->flush();

            // Répond avec un objet JSON indiquant le succès de la suppression
            return new JsonResponse(['success' => true]);

    
    }
}
