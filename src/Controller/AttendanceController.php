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

    // Page principale d'appel
    #[Route('/appel', name: 'app_attendance')]
    public function index(CategoryRepository $CategoryRepository): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            // Si non, vérifie si l'utilisateur a le rôle ROLE_COACH
            if (!$this->isGranted('ROLE_COACH')) {
                // Si l'utilisateur n'a aucun rôle, refuser l'accès
                throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
            }
        }

        // Rendre la vue avec les catégories pour l'appel
        return $this->render('attendance/index.html.twig', [
            'controller_name' => 'AttendanceController',
            'categories' => $CategoryRepository->findAll(),
        ]);
    }

    // Page d'appel pour une catégorie spécifique
    #[Route('/appel/{category}', name: 'app_attendance_u')]
    public function attendance(string $category, UserRepository $UserRepository): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            // Si non, vérifie si l'utilisateur a le rôle ROLE_COACH
            if (!$this->isGranted('ROLE_COACH')) {
                // Si l'utilisateur n'a aucun rôle, refuser l'accès
                throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
            }
        }

        // Récupère tous les utilisateurs depuis le référentiel pour la catégorie donnée
        $allUsers = $UserRepository->findAll($category);

        // Filtrer les utilisateurs qui appartiennent à la catégorie spécifiée
        $usersInCategory = array_filter($allUsers, function ($user) use ($category) {
            return $user->getCategory() === $category;
        });

        // Rendre le modèle en fonction de la catégorie
        return $this->render('attendance/attendance.html.twig', [
            'controller_name' => 'AttendanceController',
            'category' => $category,
            'users' => $usersInCategory,
        ]);
    }

    #[Route('/create-attendance-{category}', name: 'create_attendance', methods: ['POST'])]
    public function createAttendance(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            // Si non, vérifie si l'utilisateur a le rôle ROLE_COACH
            if (!$this->isGranted('ROLE_COACH')) {
                // Si l'utilisateur n'a aucun rôle, refuser l'accès
                throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
            }
        }

        // Analyse les données JSON de la requête
        $requestData = json_decode($request->getContent(), true);

        $presentUserIds = $requestData['presentUserIds'];
        $absentUserIds = $requestData['absentUserIds'];
        $categoryName = $requestData['category'];
        $happenedDate = new DateTime($requestData['datetime'], new DateTimeZone('Europe/Paris'));
        $type = $requestData['type'];

        // Trouver l'entité Category par le nom
        $category = $entityManager->getRepository(Category::class)->findOneBy(['name' => $categoryName]);

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

        return new JsonResponse(['message' => 'Matches played updated successfully']);
    }

    #[Route('/modify-attendance/{gathering}', name: 'modify_attendance', methods: ['GET'])]
    public function modifyAttendance(string $gathering, UserRepository $UserRepository, AttendanceRepository $attendanceRepository): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            // Si non, vérifie si l'utilisateur a le rôle ROLE_COACH
            if (!$this->isGranted('ROLE_COACH')) {
                // Si l'utilisateur n'a aucun rôle, refuser l'accès
                throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
            }
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
            'category' => $category,
            'users' => $usersInCategory,
            'gathering' => $gathering,
            'attendances' => $attendances,
        ]);
    }

    #[Route('/update-attendance/{gathering}', name: 'update_attendance', methods: ['POST'])]
    public function updateAttendance(Request $request, Gathering $gathering): JsonResponse
    {
        // Get data from the request
        $requestData = json_decode($request->getContent(), true);
        $presentUserIds = $requestData['presentUserIds'];
        $absentUserIds = $requestData['absentUserIds'];
        $type = $requestData['type'];
        $datetime = new \DateTime($requestData['datetime']);

        // Get the EntityManager
        $entityManager = $this->getDoctrine()->getManager();

        try {
            // Update the gathering details
            $gathering->setType($type);
            $gathering->setGatheringHappenedDate($datetime);

            // Update every attendance details one by one
            foreach ($gathering->getAttendances() as $attendance) {
                // Update attendance details
                // $attendance->setIsPresent(False);
                // $attendance->setReason("test");

                // $entityManager->persist($attendance);

                // Update attendance records for present users
                foreach ($presentUserIds as $userId) {

                    $user = $entityManager->getRepository(User::class)->find($userId);

                    error_log("Présent - userId = " . $userId);
                    error_log("Présent - user->getId() = " . $user->getId());

                    if ($user->getId() == $userId) {
                        $attendance->setIsPresent(true);
                        $attendance->setReason(null);
                        $entityManager->persist($attendance);
                    }
                }

                // Update attendance records for absent users
                foreach ($absentUserIds as $userData) {
                    $userId = $userData['userId'];
                    $reason = $userData['reason'];

                    $user = $entityManager->getRepository(User::class)->find($userId);

                    error_log("Absent - userId = " . $userId);
                    error_log("Absent - user->getId() = " . $user->getId());

                    if ($user->getId() == $userId) {
                        $attendance->setIsPresent(false);
                        $attendance->setReason($reason);
                        $entityManager->persist($attendance);
                    }
                }
            }

            // Commit changes to the database
            $entityManager->flush();

            return new JsonResponse(['message' => 'Gathering attendance updated successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred during attendance update'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/update-attendance2/{gathering}', name: 'update_attendance2', methods: ['POST'])]
    public function updateAttendance2(Request $request, Gathering $gathering, AttendanceRepository $attendanceRepository): JsonResponse
    {
        // Get data from the request
        $requestData = json_decode($request->getContent(), true);
        $presentUserIds = $requestData['presentUserIds'];
        $absentUserIds = $requestData['absentUserIds'];
        $type = $requestData['type'];
        $datetime = new \DateTime($requestData['datetime']);

        // Get the EntityManager
        $entityManager = $this->getDoctrine()->getManager();

        try {
            // Update the gathering details
            $gathering->setType($type);
            $gathering->setGatheringHappenedDate($datetime);

            // Delete existing attendance records for the gathering
            // $existingAttendances = $entityManager->getRepository(Attendance::class)->findBy(['gathering' => $gathering]);
            // $existingAttendances = $entityManager->getRepository(Attendance::class);

            // $existingAttendances = $attendanceRepository;

            // foreach ($existingAttendances as $attendance) {
            //     $entityManager->remove($attendance);
            // }

            // Commit changes to the database
            $entityManager->flush();

            // // Create new attendance records for present users
            // foreach ($presentUserIds as $userId) {
            //     $user = $entityManager->getRepository(User::class)->find($userId);

            //     if ($user) {
            //         $attendance = new Attendance();
            //         $attendance->setUser($user);
            //         $attendance->setGathering($gathering);
            //         $attendance->setIsPresent(true);
            //         $attendance->setReason(null);
            //         $entityManager->persist($attendance);
            //     }
            // }

            // // Create new attendance records for absent users
            // foreach ($absentUserIds as $userData) {
            // $userId = $userData['userId'];
            // $reason = $userData['reason'];

            //     $user = $entityManager->getRepository(User::class)->find($userId);

            //     if ($user) {
            //         $attendance = new Attendance();
            //         $attendance->setUser($user);
            //         $attendance->setGathering($gathering);
            //         $attendance->setIsPresent(false);
            //         $attendance->setReason($reason);
            //         $entityManager->persist($attendance);
            //     }
            // }

            // Commit changes to the database
            // $entityManager->flush();

            return new JsonResponse(['message' => 'Gathering attendance updated successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred during attendance update'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/update-attendances-{category}', name: 'update_attendances', methods: ['POST'])]
    public function updateAttendances(string $gathering, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            // Si non, vérifie si l'utilisateur a le rôle ROLE_COACH
            if (!$this->isGranted('ROLE_COACH')) {
                // Si l'utilisateur n'a aucun rôle, refuser l'accès
                throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
            }
        }

        // Analyse les données JSON de la requête
        $requestData = json_decode($request->getContent(), true);

        $presentUserIds = $requestData['presentUserIds'];
        $absentUserIds = $requestData['absentUserIds'];
        $categoryName = $requestData['category'];
        $type = $requestData['type'];

        // Trouver l'entité Category par le nom
        $category = $entityManager->getRepository(Category::class)->findOneBy(['name' => $categoryName]);

        if (!$category) {
            return new JsonResponse(['error' => 'Catégorie invalide'], Response::HTTP_BAD_REQUEST);
        }

        // Trouver la dernière gathering pour cette catégorie
        $latestGathering = $entityManager->getRepository(Gathering::class)->findOneBy(
            ['category' => $category],
            ['gatheringDate' => 'DESC']
        );

        if (!$latestGathering) {
            return new JsonResponse(['error' => 'Aucune réunion trouvée pour cette catégorie'], Response::HTTP_NOT_FOUND);
        }

        // Mettre à jour les enregistrements de présence pour les utilisateurs présents
        foreach ($presentUserIds as $userId) {
            $user = $entityManager->getRepository(User::class)->find($userId);

            if ($user) {
                $attendance = $entityManager->getRepository(Attendance::class)->findOneBy([
                    'user' => $user,
                    'gathering' => $latestGathering
                ]);

                if ($attendance) {
                    $attendance->setIsPresent(true);
                    $attendance->setReason(null);
                } else {
                    $attendance = new Attendance();
                    $attendance->setUser($user);
                    $attendance->setGathering($latestGathering);
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

            $user = $entityManager->getRepository(User::class)->find($userId);

            if ($user) {
                $attendance = $entityManager->getRepository(Attendance::class)->findOneBy([
                    'user' => $user,
                    'gathering' => $latestGathering
                ]);

                if ($attendance) {
                    $attendance->setIsPresent(false);
                    $attendance->setReason($reason);
                } else {
                    $attendance = new Attendance();
                    $attendance->setUser($user);
                    $attendance->setGathering($latestGathering);
                    $attendance->setIsPresent(false);
                    $attendance->setReason($reason);
                    $entityManager->persist($attendance);
                }
            }
        }

        // Envoyer les modifications à la base de données
        $entityManager->flush();

        return new JsonResponse(['message' => 'Attendances updated successfully']);
    }

    // #[Route('/modify-attendance-{category}', name: 'modify_attendance', methods: ['POST'])]
    // public function modifyAttendance(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     // Check if the user has ROLE_SUPER_ADMIN or ROLE_COACH role
    //     if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
    //         throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
    //     }

    //     // Parse JSON data from the request
    //     $requestData = json_decode($request->getContent(), true);

    //     $presentUserIds = $requestData['presentUserIds'];
    //     $absentUserIds = $requestData['absentUserIds'];
    //     $categoryName = $requestData['category'];
    //     $happenedDate = new DateTime($requestData['datetime'], new DateTimeZone('Europe/Paris'));
    //     $type = $requestData['type'];

    //     // Find the Category entity by name
    //     $category = $entityManager->getRepository(Category::class)->findOneBy(['name' => $categoryName]);

    //     if (!$category) {
    //         return new JsonResponse(['error' => 'Catégorie invalide'], Response::HTTP_BAD_REQUEST);
    //     }

    //     // Create or find the gathering to modify the attendances
    //     $gathering = $entityManager->getRepository(Gathering::class)->findOneBy(['category' => $category, 'gatheringHappenedDate' => $happenedDate]);

    //     if (!$gathering) {
    //         return new JsonResponse(['error' => 'Aucune rencontre trouvée pour cette catégorie et cette date'], Response::HTTP_NOT_FOUND);
    //     }

    //     // Update gathering type
    //     $gathering->setType($type);

    //     // Update present attendances
    //     foreach ($presentUserIds as $userId) {
    //         $user = $entityManager->getRepository(User::class)->find($userId);

    //         if ($user) {
    //             $attendance = $entityManager->getRepository(Attendance::class)->findOneBy(['user' => $user, 'gathering' => $gathering]);

    //             if ($attendance) {
    //                 $attendance->setIsPresent(true);
    //                 $attendance->setReason(null);
    //             } else {
    //                 $attendance = new Attendance();
    //                 $attendance->setUser($user);
    //                 $attendance->setGathering($gathering);
    //                 $attendance->setIsPresent(true);
    //                 $attendance->setReason(null);
    //                 $entityManager->persist($attendance);
    //             }
    //         }
    //     }

    //     // Update absent attendances
    //     foreach ($absentUserIds as $userData) {
    //         $userId = $userData['userId'];
    //         $reason = $userData['reason'];

    //         $user = $entityManager->getRepository(User::class)->find($userId);

    //         if ($user) {
    //             $attendance = $entityManager->getRepository(Attendance::class)->findOneBy(['user' => $user, 'gathering' => $gathering]);

    //             if ($attendance) {
    //                 $attendance->setIsPresent(false);
    //                 $attendance->setReason($reason);
    //             } else {
    //                 $attendance = new Attendance();
    //                 $attendance->setUser($user);
    //                 $attendance->setGathering($gathering);
    //                 $attendance->setIsPresent(false);
    //                 $attendance->setReason($reason);
    //                 $entityManager->persist($attendance);
    //             }
    //         }
    //     }

    //     // Send modifications to the database
    //     $entityManager->flush();

    //     return new JsonResponse(['message' => 'Attendance modified successfully']);
    // }

    // #[Route('/update-attendance/{gathering}2', name: 'update_attendance2', methods: ['POST'])]
    // public function updateAttendance2(Request $request, Gathering $gathering): JsonResponse
    // {
    //     // Get data from the request
    //     $requestData = json_decode($request->getContent(), true);
    //     $presentUserIds = $requestData['presentUserIds'];
    //     $absentUserIds = $requestData['absentUserIds'];
    //     $type = $requestData['type'];
    //     $datetime = new \DateTime($requestData['datetime']);

    //     // Get the EntityManager
    //     $entityManager = $this->getDoctrine()->getManager();

    //     try {
    //         // Update the gathering details
    //         $gathering->setType($type);
    //         $gathering->setGatheringHappenedDate($datetime);

    //         // Update existing attendance records for users present
    //         foreach ($presentUserIds as $userId) {
    //             $user = $entityManager->getRepository(User::class)->find($userId);

    //             if ($user) {
    //                 // Check if attendance record exists for the user and gathering
    //                 $attendance = $entityManager->getRepository(Attendance::class)->findOneBy([
    //                     'user' => $user,
    //                     'gathering' => $gathering,
    //                 ]);

    //                 // If the attendance record exists, update it
    //                 if ($attendance) {
    //                     $attendance->setIsPresent(true);
    //                     $attendance->setReason(null);
    //                 } else {
    //                     // If the attendance record doesn't exist, create a new one
    //                     $attendance = new Attendance();
    //                     $attendance->setUser($user);
    //                     $attendance->setGathering($gathering);
    //                     $attendance->setIsPresent(true);
    //                     $attendance->setReason(null);
    //                     $entityManager->persist($attendance);
    //                 }
    //             }
    //         }

    //         // Update existing attendance records for users absent
    //         foreach ($absentUserIds as $userData) {
    //             $userId = $userData['userId'];
    //             $reason = $userData['reason'];

    //             $user = $entityManager->getRepository(User::class)->find($userId);

    //             if ($user) {
    //                 // Check if attendance record exists for the user and gathering
    //                 $attendance = $entityManager->getRepository(Attendance::class)->findOneBy([
    //                     'user' => $user,
    //                     'gathering' => $gathering,
    //                 ]);

    //                 // If the attendance record exists, update it
    //                 if ($attendance) {
    //                     $attendance->setIsPresent(false);
    //                     $attendance->setReason($reason);
    //                 } else {
    //                     // If the attendance record doesn't exist, create a new one
    //                     $attendance = new Attendance();
    //                     $attendance->setUser($user);
    //                     $attendance->setGathering($gathering);
    //                     $attendance->setIsPresent(false);
    //                     $attendance->setReason($reason);
    //                     $entityManager->persist($attendance);
    //                 }
    //             }
    //         }

    //         // Commit changes to the database
    //         $entityManager->flush();

    //         return new JsonResponse(['message' => 'Gathering attendance updated successfully']);
    //     } catch (\Exception $e) {
    //         return new JsonResponse(['error' => 'An error occurred during attendance update'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }

    // #[Route('/update-attendance/{gathering}', name: 'update_attendance', methods: ['POST'])]
    // public function updateAttendance(Request $request, Gathering $gathering): JsonResponse
    // {
    //     // Get data from the request
    //     $requestData = json_decode($request->getContent(), true);
    //     $presentUserIds = $requestData['presentUserIds'];
    //     $absentUserIds = $requestData['absentUserIds'];
    //     $type = $requestData['type'];
    //     $datetime = new \DateTime($requestData['datetime']);

    //     // Get the EntityManager
    //     $entityManager = $this->getDoctrine()->getManager();

    //     try {
    //         // Update the gathering details
    //         $gathering->setType($type);
    //         $gathering->setGatheringHappenedDate($datetime);

    //         // Mettre à jour les enregistrements de présence pour les utilisateurs présents
    //         foreach ($presentUserIds as $userId) {
    //             $user = $entityManager->getRepository(User::class)->find($userId);

    //             if ($user) {
    //                 $attendance = new Attendance();
    //                 $attendance->setUser($user);
    //                 $attendance->setGathering($gathering);
    //                 $attendance->setIsPresent(true);
    //                 $attendance->setReason(null);
    //                 $entityManager->persist($attendance);
    //             }
    //         }

    //         // Mettre à jour les enregistrements de présence pour les utilisateurs absents
    //         foreach ($absentUserIds as $userData) {
    //             $userId = $userData['userId'];
    //             $reason = $userData['reason'];

    //             $user = $entityManager->getRepository(User::class)->find($userId);

    //             if ($user) {
    //                 $attendance = new Attendance();
    //                 $attendance->setUser($user);
    //                 $attendance->setGathering($gathering);
    //                 $attendance->setIsPresent(false);
    //                 $attendance->setReason($reason);
    //                 $entityManager->persist($attendance);
    //             }
    //         }

    //         // Commit changes to the database
    //         $entityManager->flush();

    //         return new JsonResponse(['message' => 'Gathering attendance updated successfully']);
    //     } catch (\Exception $e) {
    //         return new JsonResponse(['error' => 'An error occurred during attendance update'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }
}
