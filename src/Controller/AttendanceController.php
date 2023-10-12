<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\PlayerRepository;
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
        // TODO replace findAll() by findAllByCategorie('U10')

        // if user choose training {
        //     $training = new training;
        // }

        // else if user choose match {
        // $match = new match;
        // }

        // Filter users who belong to the 'U10' category
        $usersInU10 = array_filter($allUsers, function ($user) {
            return $user->getCategory() === 'U10';
        });

        return $this->render('attendance/U10.html.twig', [
            'controller_name' => 'AttendanceController',
            'users' => $usersInU10,
        ]);
    }

    #[Route('/appel/U11', name: 'app_attendance_U11')]
    public function U11(UserRepository $UserRepository): Response
    {
        // Get all users from the repository
        $allUsers = $UserRepository->findAll();
        // TODO replace findAll() by findAllByCategorie('U11')

        // if user choose training {
        //     $training = new training;
        // }

        // else if user choose match {
        // $match = new match;
        // }

        // Filter users who belong to the 'U11' category
        $usersInU11 = array_filter($allUsers, function ($user) {
            return $user->getCategory() === 'U11';
        });

        return $this->render('attendance/U11.html.twig', [
            'controller_name' => 'AttendanceController',
            'users' => $usersInU11,
        ]);
    }

    #[Route('/appel/U12', name: 'app_attendance_U12')]
    public function U12(UserRepository $UserRepository): Response
    {
        // Get all users from the repository
        $allUsers = $UserRepository->findAll();
        // TODO replace findAll() by findAllByCategorie('U12')

        // if user choose training {
        //     $training = new training;
        // }

        // else if user choose match {
        // $match = new match;
        // }

        // Filter users who belong to the 'U12' category
        $usersInU12 = array_filter($allUsers, function ($user) {
            return $user->getCategory() === 'U12';
        });

        return $this->render('attendance/U12.html.twig', [
            'controller_name' => 'AttendanceController',
            'users' => $usersInU12,
        ]);
    }

    #[Route('/appel/U13', name: 'app_attendance_U13')]
    public function U13(UserRepository $UserRepository): Response
    {
        // Get all users from the repository
        $allUsers = $UserRepository->findAll();
        // TODO replace findAll() by findAllByCategorie('U13')

        // if user choose training {
        //     $training = new training;
        // }

        // else if user choose match {
        // $match = new match;
        // }

        // Filter users who belong to the 'U13' category
        $usersInU13 = array_filter($allUsers, function ($user) {
            return $user->getCategory() === 'U13';
        });

        return $this->render('attendance/U13.html.twig', [
            'controller_name' => 'AttendanceController',
            'users' => $usersInU13,
        ]);
    }

    #[Route('/update-matches-played-u10', name: 'update_matches_played_u10', methods: ['POST'])]
    public function updateMatchesPlayedU10(Request $request): Response
    {
        $selectedUserIds = json_decode($request->getContent(), true)['selectedUserIds'];
        $team = $this->entityManager->getRepository(Team::class)->find(5);
        $team->setMatchesPlayed($team->getMatchesPlayed() + 1);

        // // Log the request content
        // $content = $request->getContent();
        // $this->logger->info('PHP Selected User IDs: ' . json_encode($selectedUserIds));

        // Update the matches_played field for each selected user
        foreach ($selectedUserIds as $userId) {
            // Find the user entity by its ID
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            // Update the matches_played field
            $user->setMatchesPlayed($user->getMatchesPlayed() + 1);

            // Persist the changes to the database
            $this->entityManager->persist($user);
        }

        // Flush the changes to the database
        $this->entityManager->flush();

        // $this->logger->info("updateMatchesPlayed()");

        // Return a JSON response indicating the success of the update
        return new JsonResponse(['message' => 'PHP Matches played updated successfully']);
    }

    #[Route('/update-matches-played-u11', name: 'update_matches_played_u11', methods: ['POST'])]
    public function updateMatchesPlayedU11(Request $request): Response
    {
        $selectedUserIds = json_decode($request->getContent(), true)['selectedUserIds'];
        $team = $this->entityManager->getRepository(Team::class)->find(6);
        $team->setMatchesPlayed($team->getMatchesPlayed() + 1);

        // // Log the request content
        // $content = $request->getContent();
        // $this->logger->info('PHP Selected User IDs: ' . json_encode($selectedUserIds));

        // Update the matches_played field for each selected user
        foreach ($selectedUserIds as $userId) {
            // Find the user entity by its ID
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            // Update the matches_played field
            $user->setMatchesPlayed($user->getMatchesPlayed() + 1);

            // Persist the changes to the database
            $this->entityManager->persist($user);
        }

        // Flush the changes to the database
        $this->entityManager->flush();

        // $this->logger->info("updateMatchesPlayed()");

        // Return a JSON response indicating the success of the update
        return new JsonResponse(['message' => 'PHP Matches played updated successfully']);
    }

    #[Route('/update-matches-played-u12', name: 'update_matches_played_u12', methods: ['POST'])]
    public function updateMatchesPlayedU12(Request $request): Response
    {
        $selectedUserIds = json_decode($request->getContent(), true)['selectedUserIds'];
        $team = $this->entityManager->getRepository(Team::class)->find(7);
        $team->setMatchesPlayed($team->getMatchesPlayed() + 1);

        // // Log the request content
        // $content = $request->getContent();
        // $this->logger->info('PHP Selected User IDs: ' . json_encode($selectedUserIds));

        // Update the matches_played field for each selected user
        foreach ($selectedUserIds as $userId) {
            // Find the user entity by its ID
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            // Update the matches_played field
            $user->setMatchesPlayed($user->getMatchesPlayed() + 1);

            // Persist the changes to the database
            $this->entityManager->persist($user);
        }

        // Flush the changes to the database
        $this->entityManager->flush();

        // $this->logger->info("updateMatchesPlayed()");

        // Return a JSON response indicating the success of the update
        return new JsonResponse(['message' => 'PHP Matches played updated successfully']);
    }

    #[Route('/update-matches-played-u13', name: 'update_matches_played_u13', methods: ['POST'])]
    public function updateMatchesPlayedU13(Request $request): Response
    {
        $selectedUserIds = json_decode($request->getContent(), true)['selectedUserIds'];
        $team = $this->entityManager->getRepository(Team::class)->find(7);
        $team->setMatchesPlayed($team->getMatchesPlayed() + 1);

        // // Log the request content
        // $content = $request->getContent();
        // $this->logger->info('PHP Selected User IDs: ' . json_encode($selectedUserIds));

        // Update the matches_played field for each selected user
        foreach ($selectedUserIds as $userId) {
            // Find the user entity by its ID
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            // Update the matches_played field
            $user->setMatchesPlayed($user->getMatchesPlayed() + 1);

            // Persist the changes to the database
            $this->entityManager->persist($user);
        }

        // Flush the changes to the database
        $this->entityManager->flush();

        // $this->logger->info("updateMatchesPlayed()");

        // Return a JSON response indicating the success of the update
        return new JsonResponse(['message' => 'PHP Matches played updated successfully']);
    }
}
