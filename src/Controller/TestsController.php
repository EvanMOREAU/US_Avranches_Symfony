<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Tests;
use App\Entity\Palier;
use App\Form\TestsFormType;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Repository\TestsRepository;
use App\Repository\PalierRepository;
use App\Services\UserVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\HeightVerificationService;
use App\Services\WeightVerificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Annotation\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


#[Route('/tests')]
class TestsController extends AbstractController
{

    private $userVerificationService;
    private $heightVerificationService;
    private $weightVerificationService;
    private $entityManager;

    public function __construct(UserVerificationService $userVerificationService, HeightVerificationService $heightVerificationService, WeightVerificationService $weightVerificationService, EntityManagerInterface $entityManager){
        $this->userVerificationService = $userVerificationService;
        $this->heightVerificationService = $heightVerificationService;
        $this->weightVerificationService = $weightVerificationService; 
        $this->entityManager = $entityManager;
    }

    
    #[Route('/', name: 'app_tests_index')]
    public function index(Request $request, UserRepository $userRepository, TestsRepository $testsRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $userVerif = $this->userVerificationService->verifyUser();
        $heightVerif = $this->heightVerificationService->verifyHeight();
        $weightVerif = $this->weightVerificationService->verifyWeight();

        $tests = $testsRepository->findAll();
        
        $user = $this->getUser();
        
        $selectedUserId = $request->query->get('userId');
        $selectedCategory = $request->query->get('category');
        $usersByCategory = null;

        if ($selectedUserId && ($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_COACH'))) {
            $selectedUser = $userRepository->find($selectedUserId);
            $tests = $selectedUser ? $selectedUser->getTests() : [];
        } elseif (($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_COACH'))) {
            // Si la catégorie est définie, récupérez les joueurs en fonction de la catégorie
            if ($selectedCategory) {
                $usersByCategory = $this->getUsersByCategory($userRepository, $selectedCategory);
                $tests = $this->getTestsByCategory($userRepository, $selectedCategory);
            } else {
                $usersByCategory = $this->getUsersGroupedByCategory($userRepository);
                $tests = $this->entityManager->getRepository(Tests::class)->findAll();
            }
        } else {
            $tests = $user ? $user->getTests() : [];
        }

        $testsArray = is_array($tests) ? $tests : $tests->toArray();
        // Récupération des paramètres de tri
        $order = $request->query->get('order', 'desc');

        // Tri des tests en fonction des paramètres
        usort($testsArray, function ($a, $b) use ($order) {
            if ($order === 'asc') {
                return $a->getDate() <=> $b->getDate();
            } elseif ($order === 'desc') {
                return $b->getDate() <=> $a->getDate();
            } elseif ($order === 'alphabetical') {
                return $a->getUser()->getLastName() <=> $b->getUser()->getLastName();
            }
        });

        if($userVerif == 0 ){return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);}
        else if($userVerif == -1) {return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);} 
        else if($userVerif == 1) {
            if($heightVerif == -1){return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);}
            else if($heightVerif == 0){return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);}
            else if($heightVerif == 1){
                if($weightVerif == -1){return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);}
                else if($weightVerif == 0){return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);}
                else if($weightVerif == 1){
                    // Passage des tests triés au template Twig
                    return $this->render('tests/index.html.twig', [
                        'controller_name' => 'TestsController',
                        'location' => 'c',
                        'tests' => $testsArray,
                        'users' => $userRepository->findAll(),
                        'user' => $user,
                        'selectedUserId' => $selectedUserId,
                        'usersByCategory' => $usersByCategory, // Passer la variable usersByCategory au template Twig
                        'order' => $order, // Passer l'ordre de tri au template Twig
                    ]);
                }
            }
        }

    }
    
    #[Route('/new', name: 'app_tests_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TestsRepository $testsRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        if (!$this->userVerificationService->verifyUser()) {
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $test = new Tests();

        $user = $this->getUser(); // Obtenir l'utilisateur actuel

        $selectedUserId = $request->query->get('userId');
        $selectedCategory = $request->query->get('category');
        $usersByCategory = null;

        if ($selectedUserId == null){ $selectedUserId = 0;}
        if ($selectedCategory == null){$selectedCategory = 0;}
        
        if ($selectedUserId && $this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_COACH')) {
            $selectedUser = $userRepository->find($selectedUserId);
            $tests = $selectedUser ? $selectedUser->getTests() : [];
        } elseif ($this->isGranted('ROLE_SUPER_ADMIN') || $this->isGranted('ROLE_COACH')) {
            // Si la catégorie est définie, récupérez les joueurs en fonction de la catégorie
            if ($selectedCategory) {
                $usersByCategory = $this->getUsersByCategory($userRepository, $selectedCategory);
                $tests = $this->getTestsByCategory($userRepository, $selectedCategory);
            } else {
                $usersByCategory = $this->getUsersGroupedByCategory($userRepository);
                $tests = $testsRepository->findAll();
            }
        } else {
            $tests = $user ? $user->getTests() : [];
        }

        $testsArray = is_array($tests) ? $tests : $tests->toArray();
        // Récupération des paramètres de tri
        $order = $request->query->get('order', 'desc');

        // Tri des tests en fonction des paramètres
        usort($testsArray, function ($a, $b) use ($order) {
            if ($order === 'asc') {
                return $a->getDate() <=> $b->getDate();
            } elseif ($order === 'desc') {
                return $b->getDate() <=> $a->getDate();
            } elseif ($order === 'alphabetical') {
                return $a->getUser()->getFirstName() <=> $b->getUser()->getFirstName();
            }
        });

        // Si le formulaire est vide, récupérez le dernier test de l'utilisateur
        if ($request->getMethod() === 'POST' && empty($request->request->all())) {
            $lastTest = $testsRepository->findLastTestByUser($user);

            if ($lastTest) {
                // Remplir les champs avec les valeurs du dernier test
                $test->setVma($lastTest->getVma());
                $test->setDemicooper($lastTest->getDemicooper());
                $test->setCooper($lastTest->getCooper());
                $test->setJongleGauche($lastTest->getJongleGauche());
                $test->setJongleDroit($lastTest->getJongleDroit());
                $test->setJongleTete($lastTest->getJongleTete());
                $test->setConduiteBalle($lastTest->getConduiteBalle());
                $test->setVitesse($lastTest->getVitesse());
            }
        }

        $form = $this->createForm(TestsFormType::class, $test);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajoutez ces lignes pour définir la date actuelle
            $currentDate = new \DateTime();
            $test->setDate($currentDate);
            
            // Obtenez l'utilisateur sélectionné à partir du formulaire
            $selectedUser = $form->get('user')->getData();
        
            // Attribuez l'utilisateur au test
            $test->setUser($selectedUser);
        
            // Persistez le test en base de données
            $entityManager->persist($test);
            $entityManager->flush();
        
            return $this->redirectToRoute('app_tests_index');
        }

        // Récupérez tous les utilisateurs et triez-les par ordre alphabétique
        $users = $userRepository->findAll();
        usort($users, function($a, $b) {
            return $a->getFirstName() <=> $b->getFirstName();
        });

        // Passez les utilisateurs triés au modèle Twig
        return $this->render('tests/new.html.twig', [
            'test' => $test,
            'location' => 'c',
            'form' => $form,
            'user' => $users,
            'selectedUserId' => $selectedUserId,
            'usersByCategory' => $usersByCategory, // Passer la variable usersByCategory au template Twig
            'order' => $order, // Passer l'ordre de tri au template Twig
        ]);
    }


    #[Route('/tests/{id}/edit', name: 'app_tests_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,EntityManagerInterface $entityManagerInterface, TestsRepository $testsRepository, $id ): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $test = $testsRepository->find($id);

        if (!$test) {
            throw $this->createNotFoundException('Test non trouvé');
        }

        $form = $this->createForm(TestsFormType::class, $test);

        $form->handleRequest($request);

        if ($this->isGranted("ROLE_SUPER_ADMIN") || $this->isGranted('ROLE_COACH')) {
            // Si c'est un superadmin, utilisez l'id du joueur sélectionné depuis le formulaire
            $selectedUser = $form->get('user')->getData();
            $playerId = $selectedUser->getId();
        } else {
            // Sinon, utilisez l'id du joueur connecté
            $playerId = $this->getUser()->getId();
        }

        if ($form->isSubmitted() && $form->isValid()) {       
            // Enregistrez l'entité modifiée
            $entityManagerInterface->flush();

            // Redirection vers la page index après l'enregistrement
            return $this->redirectToRoute('app_tests_index');
        }
        
        return $this->render('tests/edit.html.twig', [
            'test' => $test,
            'location' => 'c',
            'form' => $form,
        ]);
    }
    #[Route('/{id}/delete', name: 'app_tests_delete', methods: ['GET', 'POST', 'DELETE'])]
    public function delete(Request $request, EntityManagerInterface $entityManagerInterface, TestsRepository $testsRepository, $id): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $test = $testsRepository->find($id);

        if (!$test) {
            throw $this->createNotFoundException('Test non trouvé');
        }

        $entityManagerInterface->remove($test);
        $entityManagerInterface->flush();

        // Ajoutez un message flash pour la suppression réussie
        $this->addFlash('success', 'La suppression a été réalisée avec succès.');
        return new JsonResponse(['success' => true]);
    }

    private function getTestsByCategory(UserRepository $userRepository, string $category): array
    {
        $usersGroupedByCategory = $this->getUsersGroupedByCategory($userRepository);
        
        if (isset($usersGroupedByCategory[$category])) {
            $usersInCategory = $usersGroupedByCategory[$category];
            
            $tests = [];
            
            foreach ($usersInCategory as $user) {
                $tests = array_merge($tests, $user->getTests()->toArray());
            }
            
            return $tests;
        } else {
            return [];
        }
    }

    private function getUsersGroupedByCategory(UserRepository $userRepository): array
    {
        $users = $userRepository->findAll();
        $groupedUsers = [];

        foreach ($users as $user) {
            $ageCategory = $this->getAgeCategory($user->getDateNaissance()); // Vous devez implémenter getAgeCategory

            if (!isset($groupedUsers[$ageCategory])) {
                $groupedUsers[$ageCategory] = [];
            }

            $groupedUsers[$ageCategory][] = $user;
        }

        return $groupedUsers;
    }

    private function getAgeCategory(\DateTime $birthDate): string
    {
        // Implémentez la logique pour déterminer la catégorie d'âge en fonction de la date de naissance
        // Par exemple, pour un découpage en U10, U11, U12, U13, vous pouvez utiliser l'année actuelle moins l'année de naissance
        $currentYear = (int) date('Y');
        $age = $currentYear - $birthDate->format('Y');

        if ($age < 10) {
            return 'U10';
        } elseif ($age < 11) {
            return 'U11';
        } elseif ($age < 12) {
            return 'U12';
        } elseif ($age < 13) {
            return 'U13';
        } else {
            return 'Other';
        }
    }

    #[Route('/cancel-test/{id}', name: 'app_cancel_test', methods: ['GET', 'POST'])]
    public function cancelTest(Request $request, EntityManagerInterface $entityManager, $id): JsonResponse
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }
        
        // Récupérez le test à partir de l'ID
        $test = $entityManager->getRepository(Tests::class)->find($id);
        
        if (!$test) {
            throw $this->createNotFoundException('Test non trouvé');
        }

        // Enregistrez les modifications dans la base de données
        $entityManager->flush();

        // Répondez avec un JSON indiquant le succès de l'opération
        return new JsonResponse(['success' => true]);
    }


    #[Route('/tests/last/{userId}', name: 'app_cancel_last', methods: ['GET', 'POST'])]
    public function getLastTestForUser(TestsRepository $testRepository, $userId)
    {
        // Recherche du dernier test de l'utilisateur spécifié
        $lastTest = $testRepository->findOneBy(['user' => $userId], ['date' => 'DESC']);

        // Vérifier si un test a été trouvé
        if (!$lastTest) {
            // Aucun test trouvé, retourner une réponse JSON avec un message d'erreur
            return $this->json(['message' => 'Aucun test trouvé pour cet utilisateur.'], Response::HTTP_NOT_FOUND);
        }

        // Test trouvé, retourner les données du test au format JSON
        return $this->json($lastTest, Response::HTTP_OK, [], ['groups' => 'test_details']);
    }

}
