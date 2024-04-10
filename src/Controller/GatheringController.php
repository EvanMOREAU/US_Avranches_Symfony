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
use App\Repository\GatheringRepository;
use App\Repository\AttendanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/rassemblement')]
class GatheringController extends AbstractController
{
    // Affiche la liste des rassemblements
    #[Route('/', name: 'app_gathering')]
    public function index(GatheringRepository $GatheringRepository, CategoryRepository $CategoryRepository, AttendanceRepository $attendanceRepository): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        // Affiche la vue avec la liste des rassemblements
        return $this->render('gathering/index.html.twig', [
            'controller_name' => 'GatheringController',
            'gatherings' => $GatheringRepository->findAll(),
            'categories' => $CategoryRepository->findAll(),
            'attendances' =>  $attendanceRepository->findall(),
            'location' => 'h',
        ]);
    }

    // Affiche les présences pour un rassemblement donné
    #[Route('/{gathering}', name: 'app_gathering_u')]
    public function attendance(string $gathering, AttendanceRepository $attendanceRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        // Récupère les présences pour le rassemblement donné
        $attendances = $attendanceRepository->findBy(['Gathering' => $gathering]);

        // Affiche la vue avec les présences pour le rassemblement donné
        return $this->render('gathering/gathering.html.twig', [
            'controller_name' => 'GatheringController',
            'attendances' => $attendances,
            'gathering' => $gathering,
            'location' => 'h',
        ]);
    }
}
