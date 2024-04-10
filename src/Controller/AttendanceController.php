<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Gathering;
use App\Entity\Attendance;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\AttendanceRepository;
use App\Repository\EquipeRepository;
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

    // Page principale d'appel qui affiche les catégories
    #[Route('/appel', name: 'app_attendance')]
    public function index(CategoryRepository $CategoryRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        // Rendre la vue avec les catégories pour l'appel
        return $this->render('attendance/index.html.twig', [
            'controller_name' => 'AttendanceController',
            'location' => 'g',
            'categories' => $CategoryRepository->findAll(),
        ]);
    }

    // Page du choix d'appel (entraînement ou match)
    #[Route('/appel/choix/{category}', name: 'app_attendance_choice')]
    public function choice(string $category): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        // Rendre le modèle en fonction de la catégorie
        return $this->render('attendance/choice_attendance.html.twig', [
            'controller_name' => 'AttendanceController',
            'location' => 'g',
            'category' => $category,
        ]);
    }

    // Page d'appel pour un entraînement
    #[Route('/appel/entraînement/{category}', name: 'app_attendance_training')]
    public function training(string $category, UserRepository $UserRepository): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        // Récupère tous les utilisateurs depuis le référentiel pour la catégorie donnée
        $allUsers = $UserRepository->findAll($category);

        // Filtrer les utilisateurs qui appartiennent à la catégorie spécifiée
        $usersInCategory = array_filter($allUsers, function ($user) use ($category) {
            return $user->getCategory() === $category;
        });

        // Rendre le modèle en fonction de la catégorie
        return $this->render('attendance/training_attendance.html.twig', [
            'controller_name' => 'AttendanceController',
            'location' => 'g',
            'category' => $category,
            'users' => $usersInCategory,
        ]);
    }

    // Page d'appel pour un match
    #[Route('/appel/match/{category}', name: 'app_attendance_match_choice')]
    public function choiceMatch(string $category, UserRepository $UserRepository, EquipeRepository $equipeRepository, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        // $category = "U10" / "U11" / ...

        // Extract the numerical part of the category (removing the 'U')
        $categoryNumber = intval(substr($category, 1));

        // Trouver l'entité Category par le nom
        $this_year = new \DateTime('now');
        $result = $this_year->format('Y');
        $name = $result - $categoryNumber + 1;
        $findCategory = $entityManager->getRepository(Category::class)->findOneBy(['name' => $name]);

        // Check if category exists
        if (!$findCategory) {
            throw $this->createNotFoundException('Category non trouvée');
        }
        
        // Now you have the $category entity, you can access its ID
        $categoryId = $findCategory->getId();

        // Query EquipeRepository to find all Equipe entities with this category ID
        $allTeams = $equipeRepository->findBy(['category' => $categoryId]);

        // Rendre le modèle en fonction de la catégorie
        return $this->render('attendance/match_choice_attendance.html.twig', [
            'controller_name' => 'AttendanceController',
            'location' => 'g',
            'category' => $category,
            'teams' => $allTeams,
        ]);
    }

    // Page d'appel pour un match
    #[Route('/appel/match/{category}/{team}/{teamid}', name: 'app_attendance_match')]
    public function match(string $category, string $team, string $teamid, UserRepository $UserRepository, EquipeRepository $equipeRepository, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        // Get the team entity based on teamid
        $teamEntity = $equipeRepository->find($teamid);

        if (!$teamEntity) {
            throw $this->createNotFoundException('Équipe non trouvée');
        }

        // Get all users belonging to the team
        $usersInTeam = $UserRepository->findBy(['equipe' => $teamEntity]);

        // Rendre le modèle en fonction de la catégorie
        return $this->render('attendance/match_attendance.html.twig', [
            'controller_name' => 'AttendanceController',
            'location' => 'g',
            'category' => $category,
            'teams' => $team,
            'teamId' => $teamid,
            'users' => $usersInTeam,
        ]);
    }

    // Crée un nouvel entraînement avec la catégorie et les joueurs
    #[Route('/create-training-attendance-{category}', name: 'create_training_attendance', methods: ['POST'])]
    public function createTrainingAttendance(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        // Analyse les données JSON de la requête
        $requestData = json_decode($request->getContent(), true);

        $presentUserIds = $requestData['presentUserIds'];
        $absentUserIds = $requestData['absentUserIds'];
        $categoryName = $requestData['category'];
        $happenedDate = new DateTime($requestData['datetime'], new DateTimeZone('Europe/Paris'));
        $type = $requestData['type'];

        // Trouver l'entité Category par le nom
        $this_year = new \DateTime('now');
        $result = $this_year->format('Y');
        $name = $result - $categoryName + 1;
        $category = $entityManager->getRepository(Category::class)->findOneBy(['name' => $name]);

        if (!$category) {
            return new JsonResponse(['error' => 'Catégorie invalide'], Response::HTTP_BAD_REQUEST);
        }

        // Créer un nouveau gathering pour enregistrer les présences
        $gathering = new Gathering();
        $parisTimezone = new DateTimeZone('Europe/Paris');
        $gathering->setGatheringDate(new \DateTime('now', $parisTimezone));
        $gathering->setGatheringHappenedDate($happenedDate);
        $gathering->setCategory($category);
        $gathering->setType($type);

        // Enregistre l'utilisateur qui a fait l'appel
        $madeByUserId = $this->getUser();
        $madeByUser = $entityManager->getRepository(User::class)->find($madeByUserId);

        if ($madeByUser) {
            $gathering->setMadeBy($madeByUser);
        } else {
            return new JsonResponse(['error' => 'Utilisateur invalide'], Response::HTTP_BAD_REQUEST);
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

        return new JsonResponse(['message' => 'Entraînement créée avec succès']);
    }

    // Affichage la page permettant la modification d'appel
    #[Route('/modify-attendance/{gathering}', name: 'modify_attendance', methods: ['GET'])]
    public function modifyAttendance(string $gathering, UserRepository $UserRepository, AttendanceRepository $attendanceRepository, ): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        // Récupère les présences pour le rassemblement donné
        $attendances = $attendanceRepository->findBy(['Gathering' => $gathering]);
        $category = $attendances[0]->getUser()->getCategory();

        // Récupère tous les utilisateurs depuis le référentiel pour la catégorie donnée
        $allUsers = $UserRepository->findAll($category);

        // Filtrer les utilisateurs qui appartiennent à la catégorie spécifiée
        $usersInCategory = array_filter($allUsers, function ($user) use ($category) {
            return $user->getCategory() === $category;
        });

        // Rendre le modèle en fonction de la catégorie
        return $this->render('attendance/modify_attendance.html.twig', [
            'controller_name' => 'AttendanceController',
            'location' => 'g',
            'category' => $category,
            'users' => $usersInCategory,
            'gathering' => $gathering,
            'attendances' => $attendances,
        ]);
    }

    // Modifie l'appel pour le rassemblement de l'équipe choisie par le coach
    #[Route('/update-attendance/{gathering}', name: 'update_attendance', methods: ['POST'])]
    public function updateAttendance(string $gathering, UserRepository $userRepository, AttendanceRepository $attendanceRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }
        // Récupérer le gathering spécifié
        $gatheringEntity = $entityManager->getRepository(Gathering::class)->find($gathering);

        if (!$gatheringEntity) {
            return new JsonResponse(['error' => 'Gathering not found'], Response::HTTP_NOT_FOUND);
        }

        // Analyser les données JSON de la requête
        $requestData = json_decode($request->getContent(), true);

        $parisTimezone = new DateTimeZone('Europe/Paris');
        $datetime = new \DateTime($requestData['datetime'], $parisTimezone);

        $presentUserIds = $requestData['presentUserIds'];
        $absentUserIds = $requestData['absentUserIds'];

        $gatheringEntity->setGatheringHappenedDate($datetime);

        // Mettre à jour les enregistrements de présence pour les utilisateurs présents
        foreach ($presentUserIds as $userId) {
            $user = $userRepository->find($userId);

            if ($user) {
                $attendance = $attendanceRepository->findOneBy(['User' => $user, 'Gathering' => $gatheringEntity]);
                if ($attendance) {
                    $attendance->setIsPresent(true);
                    $attendance->setReason(null);
                    $entityManager->persist($attendance);
                }
            }
        }

        // Mettre à jour les enregistrements de présence pour les utilisateurs absents
        foreach ($absentUserIds as $userData) {
            $userId = $userData['userId'];
            $reason = $userData['reason'];

            $user = $userRepository->find($userId);

            if ($user) {
                $attendance = $attendanceRepository->findOneBy(['User' => $user, 'Gathering' => $gatheringEntity]);
                if ($attendance) {
                    $attendance->setIsPresent(false);
                    $attendance->setReason($reason);
                    $entityManager->persist($attendance);
                }
            }
        }

        // Envoyer les modifications à la base de données
        $entityManager->flush();

        return new JsonResponse(['message' => 'Attendance modified successfully']);
    }
}
