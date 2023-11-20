<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Gathering;
use App\Entity\Attendance;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AttendanceController extends AbstractController
{
    private $logger;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/appel', name: 'app_attendance')]
    public function index(CategoryRepository $CategoryRepository): Response
    {
        return $this->render('attendance/index.html.twig', [
            'controller_name' => 'AttendanceController',
            'categories' => $CategoryRepository->findAll(),
        ]);
    }

    #[Route('/appel/{category}', name: 'app_attendance_u')]
    public function attendance(string $category, UserRepository $UserRepository): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            // Si non, vérifie si l'utilisateur a le rôle ROLE_COACH
             if (!$this->isGranted('ROLE_COACH')) {
                 // Si l'utilisateur n'a aucun rôle, refuser l'accès
                 throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
             }
         }
         
        // Récupère tous les utilisateurs depuis le référentiel pour la catégorie donnée
        $allUsers = $UserRepository->findAll($category);

        // Filtrer les utilisateurs qui appartiennent à la catégorie spécifiée
        $usersInCategory = array_filter($allUsers, function ($user) use ($category) {
            return $user->getCategory() === $category;
        });

        // Rendre le modèle en fonction de la catégorie
        return $this->render('attendance/attendance.html.twig', [
            'controller_name' => 'AttendanceController',
            'category' => $category,
            'users' => $usersInCategory,
        ]);
    }

    // À FAIRE : changer le code pour que les "selectedUserIds" aient "is_present=true" dans la base de données
    // et "reason=null" dans la base de données puis "unselectedUserIds" aient "is_present=false" dans la base de données
    // et "reason=La Raison à récupérer depuis la page" dans la base de données, puis que ça crée un nouveau gathering
    // dans la table tbl_gathering avec la date et la catégorie qui a joué.

    #[Route('/update-matches-played-{category}', name: 'update_matches_played', methods: ['POST'])]
    public function updateMatchesPlayed(Request $request, EntityManagerInterface $entityManager): Response
    {
        $requestData = json_decode($request->getContent(), true);

        $presentUserIds = $requestData['presentUserIds'];
        $absentUserIds = $requestData['absentUserIds'];
        $categoryName = $requestData['category'];

        // Trouver l'entité Category par le nom
        $category = $entityManager->getRepository(Category::class)->findOneBy(['name' => $categoryName]);

        if (!$category) {
            return new JsonResponse(['error' => 'Catégorie invalide'], Response::HTTP_BAD_REQUEST);
        }

        // Créer un nouveau gathering
        $gathering = new Gathering();
        $gathering->setGatheringDate(new \DateTime());
        $gathering->setCategory($category); // Utilise l'instance de l'entité Category

        // Trouver l'utilisateur (MadeBy) - Vous devrez peut-être ajuster cela en fonction de votre logique
        $madeByUserId = 1; // En supposant que vous avez l'ID de l'utilisateur qui a créé la rencontre
        $madeByUser = $entityManager->getRepository(User::class)->find($madeByUserId);

        if ($madeByUser) {
            $gathering->setMadeBy($madeByUser);
        } else {
            return new JsonResponse(['error' => 'Utilisateur invalide pour MadeBy'], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($gathering);

        // Mettre à jour les enregistrements de présence pour les utilisateurs présents
        foreach ($presentUserIds as $userId) {
            $user = $entityManager->getRepository(User::class)->find($userId);

            if ($user) {
                $attendance = new Attendance();
                $attendance->setUser($user);
                $attendance->setGathering($gathering);
                $attendance->setIsPresent(true);
                $attendance->setReason(null);
                $entityManager->persist($attendance);
            }
        }

        // Mettre à jour les enregistrements de présence pour les utilisateurs absents
        foreach ($absentUserIds as $userData) {
            $userId = $userData['userId'];
            $reason = $userData['reason'];

            $user = $entityManager->getRepository(User::class)->find($userId);

            if ($user) {
                $attendance = new Attendance();
                $attendance->setUser($user);
                $attendance->setGathering($gathering);
                $attendance->setIsPresent(false);
                $attendance->setReason($reason);
                $entityManager->persist($attendance);
            }
        }

        // Envoyer les modifications à la base de données
        $entityManager->flush();

        return new JsonResponse(['message' => 'Matches played updated successfully']);
    }
}
