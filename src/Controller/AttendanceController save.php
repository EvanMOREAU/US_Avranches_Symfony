<?php

namespace App\Controller;

use App\Entity\User;
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
        return $this->render('attendance/' . $category . '.html.twig', [
            'controller_name' => 'AttendanceController',
            'category' => $category,
            'users' => $usersInCategory,
        ]);
    }

    // TO DO, changer le code pour que les "selectedUserIds" aient "is_present=true" et "reason=null" puis "unselectedUserIds" aient "is_present=false" et "reason=raison", puis que ça crée un nouveau gathering dans la table tbl_gathering avec la date et la catégorie qui a joué.

    // #[Route('/update-matches-played-{category}', name: 'update_matches_played', methods: ['POST'])]
    // public function updateMatchesPlayed(string $category, Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $selectedUserIds = json_decode($request->getContent(), true)['selectedUserIds'];
    //     $unselectedUserIds = json_decode($request->getContent(), true)['unselectedUserIds'];

    //     // Determine the category ID based on the category
    //     $categoryId = $entityManager->getRepository(Category::class)->findOneBy(['category' => $category]);

    //     if ($categoryId !== null) {
    //         $category = $entityManager->getRepository(Category::class)->find($categoryId);

    //         if ($category) {
    //             // Create Attendance records for selected users
    //             foreach ($selectedUserIds as $userId) {
    //                 $user = $entityManager->getRepository(User::class)->find($userId);

    //                 if ($user) {
    //                     $attendance = new Attendance();
    //                     $attendance->setUser($user);
    //                     $attendance->setGathering($category->getGathering()); // Adjust this part based on your structure
    //                     $attendance->setIsPresent(true); // Set the attendance status
    //                     $entityManager->persist($attendance);
    //                 }
    //             }

    //             // Flush the changes to the database
    //             $entityManager->flush();

    //             return new JsonResponse(['message' => 'PHP Matches played updated successfully']);
    //         }
    //     }

    //     return new JsonResponse(['error' => 'Invalid category'], Response::HTTP_BAD_REQUEST);
    // }
}
