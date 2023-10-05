<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PlayerRepository;
use App\Repository\UserRepository;
use App\Repository\TeamRepository;

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
    public function U11(TeamRepository $TeamRepository, PlayerRepository $PlayerRepository): Response
    {
        return $this->render('attendance/U11.html.twig', [
            'controller_name' => 'AttendanceController',
            'tbl_team' => $TeamRepository->findAll(),
            'tbl_player' => $PlayerRepository->findByTeam(2),
        ]);
    }

    #[Route('/appel/U12', name: 'app_attendance_U12')]
    public function U12(TeamRepository $TeamRepository, PlayerRepository $PlayerRepository): Response
    {
        return $this->render('attendance/U12.html.twig', [
            'controller_name' => 'AttendanceController',
            'tbl_team' => $TeamRepository->findAll(),
            'tbl_player' => $PlayerRepository->findByTeam(3),
        ]);
    }

    #[Route('/appel/U13', name: 'app_attendance_U13')]
    public function U13(TeamRepository $TeamRepository, PlayerRepository $PlayerRepository): Response
    {
        return $this->render('attendance/U13.html.twig', [
            'controller_name' => 'AttendanceController',
            'tbl_team' => $TeamRepository->findAll(),
            'tbl_player' => $PlayerRepository->findByTeam(4),
        ]);
    }
}
