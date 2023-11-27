<?php

namespace App\Controller;

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
    #[Route('/', name: 'app_gathering')]
    public function index(GatheringRepository $GatheringRepository): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            // Si non, vérifie si l'utilisateur a le rôle ROLE_COACH
            if (!$this->isGranted('ROLE_COACH')) {
                // Si l'utilisateur n'a aucun rôle, refuser l'accès
                throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
            }
        }

        return $this->render('gathering/index.html.twig', [
            'controller_name' => 'GatheringController',
            'gatherings' => $GatheringRepository->findAll(),
        ]);
    }

    #[Route('/{gathering}', name: 'app_gathering_u')]
    public function attendance(string $gathering, AttendanceRepository $attendanceRepository): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            // Si non, vérifie si l'utilisateur a le rôle ROLE_COACH
            if (!$this->isGranted('ROLE_COACH')) {
                // Si l'utilisateur n'a aucun rôle, refuser l'accès
                throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
            }
        }

        $attendances = $attendanceRepository->findBy(['Gathering' => $gathering]);

        return $this->render('gathering/gathering.html.twig', [
            'controller_name' => 'GatheringController',
            'attendances' => $attendances,
            'gathering' => $gathering,
        ]);
    }

    // #[Route('/update-matches-played-{category}', name: 'update_matches_played', methods: ['POST'])]
    // public function updateMatchesPlayed(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
    //     if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
    //         // Si non, vérifie si l'utilisateur a le rôle ROLE_COACH
    //         if (!$this->isGranted('ROLE_COACH')) {
    //             // Si l'utilisateur n'a aucun rôle, refuser l'accès
    //             throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
    //         }
    //     }

    //     $requestData = json_decode($request->getContent(), true);

    //     $presentUserIds = $requestData['presentUserIds'];
    //     $absentUserIds = $requestData['absentUserIds'];
    //     $categoryName = $requestData['category'];

    //     // Trouver l'entité Category par le nom
    //     $category = $entityManager->getRepository(Category::class)->findOneBy(['name' => $categoryName]);

    //     if (!$category) {
    //         return new JsonResponse(['error' => 'Catégorie invalide'], Response::HTTP_BAD_REQUEST);
    //     }

    //     // Créer un nouveau gathering
    //     $gathering = new Gathering();
    //     $gathering->setGatheringDate(new \DateTime());
    //     $gathering->setCategory($category); // Utilise l'instance de l'entité Category

    //     // Trouver l'utilisateur (MadeBy) - Vous devrez peut-être ajuster cela en fonction de votre logique
    //     $madeByUserId = 1; // En supposant que vous avez l'ID de l'utilisateur qui a créé la rencontre
    //     $madeByUser = $entityManager->getRepository(User::class)->find($madeByUserId);

    //     if ($madeByUser) {
    //         $gathering->setMadeBy($madeByUser);
    //     } else {
    //         return new JsonResponse(['error' => 'Utilisateur invalide pour MadeBy'], Response::HTTP_BAD_REQUEST);
    //     }

    //     $entityManager->persist($gathering);

    //     // Mettre à jour les enregistrements de présence pour les utilisateurs présents
    //     foreach ($presentUserIds as $userId) {
    //         $user = $entityManager->getRepository(User::class)->find($userId);

    //         if ($user) {
    //             $attendance = new Attendance();
    //             $attendance->setUser($user);
    //             $attendance->setGathering($gathering);
    //             $attendance->setIsPresent(true);
    //             $attendance->setReason(null);
    //             $entityManager->persist($attendance);
    //         }
    //     }

    //     // Mettre à jour les enregistrements de présence pour les utilisateurs absents
    //     foreach ($absentUserIds as $userData) {
    //         $userId = $userData['userId'];
    //         $reason = $userData['reason'];

    //         $user = $entityManager->getRepository(User::class)->find($userId);

    //         if ($user) {
    //             $attendance = new Attendance();
    //             $attendance->setUser($user);
    //             $attendance->setGathering($gathering);
    //             $attendance->setIsPresent(false);
    //             $attendance->setReason($reason);
    //             $entityManager->persist($attendance);
    //         }
    //     }

    //     // Envoyer les modifications à la base de données
    //     $entityManager->flush();

    //     return new JsonResponse(['message' => 'Matches played updated successfully']);
    // }
}
