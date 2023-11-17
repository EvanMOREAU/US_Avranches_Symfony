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
        // Get all users from the repository for the given category
        $allUsers = $UserRepository->findAll($category);

        // Filter users who belong to the specified category
        $usersInCategory = array_filter($allUsers, function ($user) use ($category) {
            return $user->getCategory() === $category;
        });

        // Render the template based on the category
        return $this->render('attendance/attendance.html.twig', [
            'controller_name' => 'AttendanceController',
            'category' => $category,
            'users' => $usersInCategory,
        ]);
    }

    // TO DO, changer le code pour que les "selectedUserIds" aient "is_present=true" dans la base de données et "reason=null" dans la base de données puis "unselectedUserIds" aient "is_present=false" dans la base de données et "reason=La Raison à récupérer depuis la page" dans la base de données, puis que ça crée un nouveau gathering dans la table tbl_gathering avec la date et la catégorie qui a joué.

    #[Route('/update-matches-played-{category}', name: 'update_matches_played', methods: ['POST'])]
    public function updateMatchesPlayed(Request $request, EntityManagerInterface $entityManager): Response
    {
        $requestData = json_decode($request->getContent(), true);

        $presentUserIds = $requestData['presentUserIds'];
        $absentUserIds = $requestData['absentUserIds'];
        $categoryName = $requestData['category'];

        // Find the Category entity by name
        $category = $entityManager->getRepository(Category::class)->findOneBy(['name' => $categoryName]);

        if (!$category) {
            return new JsonResponse(['error' => 'Invalid category'], Response::HTTP_BAD_REQUEST);
        }

        // Create a new gathering
        $gathering = new Gathering();
        $gathering->setGatheringDate(new \DateTime());
        $gathering->setCategory($category); // Use the Category entity instance

        // Find the user (MadeBy) - You may need to adjust this based on your logic
        $madeByUserId = 1; // Assuming you have the ID of the user who made the gathering
        $madeByUser = $entityManager->getRepository(User::class)->find($madeByUserId);

        if ($madeByUser) {
            $gathering->setMadeBy($madeByUser);
        } else {
            return new JsonResponse(['error' => 'Invalid user for MadeBy'], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($gathering);

        // Update Attendance records for present users
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

        // Update Attendance records for absent users
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

        // Flush the changes to the database
        $entityManager->flush();

        return new JsonResponse(['message' => 'Matches played updated successfully']);
    }
}
