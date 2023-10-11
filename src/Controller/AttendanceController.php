<?php

namespace App\Controller;

use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\PlayerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AttendanceController extends AbstractController
{
    #[Route('/appel', name: 'app_attendance')]
    public function index(TeamRepository $TeamRepository): Response
    {
        return $this->render('attendance/index.html.twig', [
            'controller_name' => 'AttendanceController',
            'teams' => $TeamRepository->findAll(),
        ]);
    }

    #[Route('/appel/U10', name: 'app_attendance_U10')]
    public function U10(UserRepository $UserRepository): Response
    {
        // Get all users from the repository
        $allUsers = $UserRepository->findAll();

        // Filter users who belong to the 'U10' category
        $usersInU10 = array_filter($allUsers, function ($user) {
            return $user->getCategory() === 'U10';
        });

        return $this->render('attendance/U10.html.twig', [
            'controller_name' => 'AttendanceController',
            'users' => $usersInU10,
        ]);
    }

    // #[Route('/appel/U10', name: 'app_attendance_U10')]
    // public function U10(TeamRepository $TeamRepository, PlayerRepository $PlayerRepository): Response
    // {
    //     return $this->render('attendance/U10.html.twig', [
    //         'controller_name' => 'AttendanceController',
    //         'tbl_team' => $TeamRepository->findAll(),
    //         'tbl_player' => $PlayerRepository->findByTeam(1),
    //     ]);
    // }

    #[Route('/appel/U11', name: 'app_attendance_U11')]
    public function U11(UserRepository $UserRepository): Response
    {
        // Get all users from the repository
        $allUsers = $UserRepository->findAll();

        // Filter users who belong to the 'U11' category
        $usersInU11 = array_filter($allUsers, function ($user) {
            return $user->getCategory() === 'U11';
        });

        return $this->render('attendance/U11.html.twig', [
            'controller_name' => 'AttendanceController',
            'users' => $usersInU11,
        ]);
    }

    // #[Route('/appel/U11', name: 'app_attendance_U11')]
    // public function U11(TeamRepository $TeamRepository, PlayerRepository $PlayerRepository): Response
    // {
    //     return $this->render('attendance/U11.html.twig', [
    //         'controller_name' => 'AttendanceController',
    //         'tbl_team' => $TeamRepository->findAll(),
    //         'tbl_player' => $PlayerRepository->findByTeam(2),
    //     ]);
    // }

    #[Route('/appel/U12', name: 'app_attendance_U12')]
    public function U12(UserRepository $UserRepository): Response
    {
        // Get all users from the repository
        $allUsers = $UserRepository->findAll();

        // Filter users who belong to the 'U12' category
        $usersInU12 = array_filter($allUsers, function ($user) {
            return $user->getCategory() === 'U12';
        });

        return $this->render('attendance/U12.html.twig', [
            'controller_name' => 'AttendanceController',
            'users' => $usersInU12,
        ]);
    }

    // #[Route('/appel/U12', name: 'app_attendance_U12')]
    // public function U12(TeamRepository $TeamRepository, PlayerRepository $PlayerRepository): Response
    // {
    //     return $this->render('attendance/U12.html.twig', [
    //         'controller_name' => 'AttendanceController',
    //         'tbl_team' => $TeamRepository->findAll(),
    //         'tbl_player' => $PlayerRepository->findByTeam(3),
    //     ]);
    // }

    #[Route('/appel/U13', name: 'app_attendance_U13')]
    public function U13(UserRepository $UserRepository): Response
    {
        // Get all users from the repository
        $allUsers = $UserRepository->findAll();

        // Filter users who belong to the 'U13' category
        $usersInU13 = array_filter($allUsers, function ($user) {
            return $user->getCategory() === 'U13';
        });

        return $this->render('attendance/U13.html.twig', [
            'controller_name' => 'AttendanceController',
            'users' => $usersInU13,
        ]);
    }

    // #[Route('/appel/U13', name: 'app_attendance_U13')]
    // public function U13(TeamRepository $TeamRepository, PlayerRepository $PlayerRepository): Response
    // {
    //     return $this->render('attendance/U13.html.twig', [
    //         'controller_name' => 'AttendanceController',
    //         'tbl_team' => $TeamRepository->findAll(),
    //         'tbl_player' => $PlayerRepository->findByTeam(4),
    //     ]);
    // }

    #[Route('/update-matches-played', name: 'update_matches_played', methods: ['POST'])]
    public function updateMatchesPlayed(Request $request, UserRepository $userRepository): RedirectResponse
    {
        // Get the selected user IDs from the POST data
        $selectedUserIds = json_decode($request->getContent())->selectedUserIds;

        // Update matches_played for users who are not selected
        $userRepository->incrementMatchesPlayedForUnselectedUsers($selectedUserIds);

        // Add a flash message to indicate success
        $this->addFlash('success', 'Matches played updated successfully!');

        return $this->redirectToRoute('app_attendance_U10'); // Redirect to the U10 page or the appropriate route
    }

    // #[Route('/update-matches-played', name: 'update_matches_played', methods: ['POST'])]
    // public function updateMatchesPlayed(Request $request, UserRepository $userRepository): RedirectResponse
    // {
    //     // Get the selected user IDs from the POST data
    //     $selectedUserIds = json_decode($request->getContent())->selectedUserIds;
    //     $category = 'U10'; // Replace with the actual category of the users

    //     // Update matches_played for users who are not selected in their respective category
    //     $userRepository->incrementMatchesPlayedForUnselectedUsers($selectedUserIds, $category);

    //     // Add a flash message to indicate success
    //     $this->addFlash('success', 'Matches played updated successfully!');

    //     return $this->redirectToRoute('app_attendance_U10'); // Redirect to the U10 page or the appropriate route
    // }
}
