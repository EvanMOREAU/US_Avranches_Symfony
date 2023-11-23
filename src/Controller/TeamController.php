<?php

// src/Controller/TeamController.php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamController extends AbstractController
{
    #[Route('/team', name: 'app_team_index', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        // Récupérer l'utilisateur connecté
        $loggedInUser = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if ($loggedInUser) {
            // Récupérer la catégorie de l'utilisateur connecté
            $category = $loggedInUser->getCategory(); // Adapté à votre méthode getCategory

            // Suppose you have a UserRepository or some service that fetches users
            $userRepository = $this->getDoctrine()->getRepository(User::class);

            // Récupérer tous les utilisateurs
            $allUsers = $userRepository->findAll();

            // Filtrer les utilisateurs ayant la même catégorie que l'utilisateur connecté
            $users = array_filter($allUsers, function ($user) use ($category) {
                return $user->getCategory() == $category;
            });

            // Passer les utilisateurs à la vue Twig
            return $this->render('team/index.html.twig', [
                'users' => $users,
            ]);
        } else {
            // L'utilisateur n'est pas connecté, vous pouvez gérer cela en conséquence
            // ...

            // Par exemple, rediriger vers une page de connexion
            return $this->redirectToRoute('login');
        }
    }
}


