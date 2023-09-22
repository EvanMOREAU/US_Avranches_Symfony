<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TblPlayersRepository;
use App\Repository\TblTeamsRepository;

class AttendanceController extends AbstractController
{
    #[Route('/attendance', name: 'app_attendance')]
    public function index(TblPlayersRepository $tblPlayersRepository, TblTeamsRepository $TblTeamsRepository): Response
    {
        return $this->render('attendance/index.html.twig', [
            'controller_name' => 'AttendanceController',
            'tbl_teams' => $TblTeamsRepository->findAll(),
        ]);
    }
}
