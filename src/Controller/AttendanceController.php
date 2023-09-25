<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TblPlayerRepository;
use App\Repository\TblTeamRepository;

class AttendanceController extends AbstractController
{
    #[Route('/appel', name: 'app_attendance')]
    public function index(TblTeamRepository $TblTeamRepository): Response
    {
        return $this->render('attendance/index.html.twig', [
            'controller_name' => 'AttendanceController',
            'tbl_team' => $TblTeamRepository->findAll(),
        ]);
    }

    
    #[Route('/appel/U10', name: 'app_attendance_U10')]
    public function U10(TblTeamRepository $TblTeamRepository, TblPlayerRepository $TblPlayerRepository): Response
    {
        return $this->render('attendance/U10.html.twig', [
            'controller_name' => 'AttendanceController',
            'tbl_team' => $TblTeamRepository->findAll(),
            'tbl_player' => $TblPlayerRepository->findByTeam(1),
        ]);
    }

    #[Route('/appel/U11', name: 'app_attendance_U11')]
    public function U11(TblTeamRepository $TblTeamRepository, TblPlayerRepository $TblPlayerRepository): Response
    {
        return $this->render('attendance/U11.html.twig', [
            'controller_name' => 'AttendanceController',
            'tbl_team' => $TblTeamRepository->findAll(),
            'tbl_player' => $TblPlayerRepository->findByTeam(2),
        ]);
    }

    #[Route('/appel/U12', name: 'app_attendance_U12')]
    public function U12(TblTeamRepository $TblTeamRepository, TblPlayerRepository $TblPlayerRepository): Response
    {
        return $this->render('attendance/U12.html.twig', [
            'controller_name' => 'AttendanceController',
            'tbl_team' => $TblTeamRepository->findAll(),
            'tbl_player' => $TblPlayerRepository->findByTeam(3),
        ]);
    }

    #[Route('/appel/U13', name: 'app_attendance_U13')]
    public function U13(TblTeamRepository $TblTeamRepository, TblPlayerRepository $TblPlayerRepository): Response
    {
        return $this->render('attendance/U13.html.twig', [
            'controller_name' => 'AttendanceController',
            'tbl_team' => $TblTeamRepository->findAll(),
            'tbl_player' => $TblPlayerRepository->findByTeam(4),
        ]);
    }
}
