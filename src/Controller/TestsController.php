<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Tests;
use App\Form\TestsFormType;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Repository\TestsRepository;
use App\Service\UserVerificationService;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(UserVerificationService $userVerificationService, TestsRepository $testsRepository){
        $this->userVerificationService = $userVerificationService;
    }

    #[Route('/', name: 'app_tests_index')]
    public function index(Request $request, UserRepository $userRepository, TestsRepository $testsRepository): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $tests = $testsRepository->findAll();
        
        $user = $this->getUser();
        $selectedUserId = $request->query->get('userId');
        $selectedCategory = $request->query->get('category');
        $usersByCategory = null;

        if ($selectedUserId && $this->isGranted('ROLE_SUPER_ADMIN')) {
            $selectedUser = $userRepository->find($selectedUserId);
            $tests = $selectedUser ? $selectedUser->getTests() : [];
        } elseif ($this->isGranted('ROLE_SUPER_ADMIN')) {
            // Si la catégorie est définie, récupérez les joueurs en fonction de la catégorie
            if ($selectedCategory) {
                $usersByCategory = $this->getUsersByCategory($userRepository, $selectedCategory);
                $tests = $this->getTestsByCategory($userRepository, $selectedCategory);
            } else {
                $usersByCategory = $this->getUsersGroupedByCategory($userRepository);
                $tests = $this->getDoctrine()->getRepository(Tests::class)->findAll();
            }
        } else {
            $tests = $user ? $user->getTests() : [];
        }

        $testsArray = is_array($tests) ? $tests : $tests->toArray();
        $order = $request->query->get('order', 'desc');
        $validated = $request->query->get('is_validated');


        usort($testsArray, function ($a, $b) use ($order) {
            if ($order === 'asc') {
                // Trie par date
                return $a->getDate() <=> $b->getDate();
            } elseif ($order === 'desc') {
                // Trie par date en ordre inverse
                return $b->getDate() <=> $a->getDate();
            } elseif ($order === 'alphabetical') {
                // Trie par prénom en ordre alphabétique
                return $a->getUser()->getFirstName() <=> $b->getUser()->getFirstName();
            }
        });
        

        return $this->render('tests/index.html.twig', [
            'controller_name' => 'TestsController',
            'tests' => $testsArray,
            'users' => $userRepository->findAll(),
            'user' => $user,
            'selectedUserId' => $selectedUserId,
            'usersByCategory' => $usersByCategory,
        ]);
    }
    
    #[Route('/new', name: 'app_tests_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TestsRepository $testsRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->userVerificationService->verifyUser()) {
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $test = new Tests();

        // Vérifier si le formulaire est vide
        $formIsEmpty = empty($request->request->all());

        // Si le formulaire est vide, récupérez le dernier test de l'utilisateur
        if ($formIsEmpty) {
            $lastTest = $testsRepository->findLastTestByUser($this->getUser());

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

            // Si l'utilisateur est superadmin, attribuez le test à l'utilisateur sélectionné depuis le formulaire
            if ($this->isGranted("ROLE_SUPER_ADMIN")) {
                $selectedUser = $form->get('user')->getData();

                if ($selectedUser) {
                    $test->setUser($selectedUser);
                }
            } else {
                // Si l'utilisateur n'est pas superadmin, attribuez le test à l'utilisateur connecté
                $test->setUser($this->getUser());
            }

            // Traitement du champ vidéo
            $videoFile = $form->get('video')->getData();
            if ($videoFile instanceof UploadedFile) {
                // Utilisez le nom fourni dans le formulaire pour enregistrer la vidéo
                $videoName = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $videoFileName = $this->uploadVideo($videoFile, $videoName);
                $test->setVideo($videoFileName);
            }


            $entityManager->persist($test);
            $entityManager->flush();

            return $this->redirectToRoute('app_tests_index');
        }

        return $this->renderForm('tests/new.html.twig', [
            'test' => $test,
            'form' => $form,
        ]);
    }


    #[Route('/tests/{id}/edit', name: 'app_tests_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function edit(Request $request, TestsRepository $testsRepository, $id): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $test = $testsRepository->find($id);

        if (!$test) {
            throw $this->createNotFoundException('Test non trouvé');
        }

        $form = $this->createForm(TestsFormType::class, $test);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush(); // Enregistrez les modifications

            // Ajoutez un message flash pour indiquer que la modification a réussi
            $this->addFlash('success', 'La modification a été réalisée avec succès.');

            // Redirigez l'utilisateur vers une autre page, par exemple la liste des tests
            return $this->redirectToRoute('app_tests_index');
        }

        return $this->renderForm('tests/edit.html.twig', [
            'test' => $test,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/delete', name: 'app_tests_delete', methods: ['GET', 'POST', 'DELETE'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, TestsRepository $testsRepository, $id): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $test = $testsRepository->find($id);

        if (!$test) {
            throw $this->createNotFoundException('Test non trouvé');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($test);
        $entityManager->flush();

        // Ajoutez un message flash pour la suppression réussie
        $this->addFlash('success', 'La suppression a été réalisée avec succès.');
        return new JsonResponse(['success' => true]);
        // Redirigez l'utilisateur vers une autre page, par exemple la liste des tests
        return $this->redirectToRoute('app_tests_index');
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
            // Ajoutez des conditions pour d'autres catégories si nécessaire
            // ...

            // Par défaut, retournez une catégorie générique
            return 'Other';
        }
    }
    #[Route('/validate-test/{id}', name: 'app_validate_test', methods: ['GET', 'POST'])]
    public function validateTestAction(EntityManagerInterface $entityManager, $id, Request $request): JsonResponse
    {
        
        // Récupérez le test à partir de l'ID
        $test = $entityManager->getRepository(Tests::class)->find($id);

        if (!$test) {
            return new JsonResponse(['success' => false, 'message' => 'Test non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Mettez à jour la propriété is_validated
        $test->setIsValidated(true); // Assurez-vous que le nom du champ correspond à votre entité
        
        
        try {
            // Enregistrez les modifications dans la base de données
            $entityManager->flush();

            // Supprimez la vidéo du dossier uploads/videos
            $videoFileName = $test->getVideo();
            $videoPath = $this->getParameter('upload_dir') . '/videos/' . $videoFileName;
            
            // Vérifiez si le fichier existe avant de tenter de le supprimer
            if ($videoFileName && file_exists($videoPath)) {
                unlink($videoPath);
            }

            // Vérifiez si la requête est une requête Ajax
            if ($request->isXmlHttpRequest()) {
                // Répondez avec un JSON indiquant le succès de l'opération
                return new JsonResponse(['success' => true]);
            } else {
                // Si ce n'est pas une requête Ajax, ajoutez un message flash pour indiquer le succès et redirigez l'utilisateur
                $this->addFlash('success', 'La validation a été réalisée avec succès.');
                return $this->redirectToRoute('app_tests_index');
            }
        } catch (\Exception $e) {
            // Gérez les erreurs et répondez en conséquence
            $errorMessage = $e->getMessage();

            // Vérifiez si la requête est une requête Ajax
            if ($request->isXmlHttpRequest()) {
                // Répondez avec un JSON indiquant l'échec de l'opération et le message d'erreur
                return new JsonResponse(['success' => false, 'message' => $errorMessage], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            } else {
                // Si ce n'est pas une requête Ajax, ajoutez un message flash pour indiquer l'échec et redirigez l'utilisateur
                $this->addFlash('error', 'Erreur lors de la validation du test: ' . $errorMessage);
                return $this->redirectToRoute('app_tests_index');
            }
        }
    }
    #[Route('/cancel-test/{id}', name: 'app_cancel_test', methods: ['GET', 'POST'])]
    public function cancelTest(Request $request, EntityManagerInterface $entityManager, $id): JsonResponse
    {
        // Récupérez le test à partir de l'ID
        $test = $entityManager->getRepository(Tests::class)->find($id);
        
        if (!$test) {
            throw $this->createNotFoundException('Test non trouvé');
        }

        // Mettez à jour la propriété is_validated
        $test->setIsValidated(false); // Assurez-vous que le nom du champ correspond à votre entité

        // Enregistrez les modifications dans la base de données
        $entityManager->flush();

        // Répondez avec un JSON indiquant le succès de l'opération
        return new JsonResponse(['success' => true]);
    }
    private function uploadVideo(UploadedFile $videoFile, string $videoName): string
    {
        // Définissez le répertoire où vous souhaitez stocker les vidéos
        $videoDirectory = $this->getParameter('upload_dir');

        // Utilisez le nom fourni dans le formulaire avec une extension "mp4"
        $newFileName = $videoName . '.mp4';

        // Déplacez le fichier dans le répertoire configuré
        $videoFile->move($videoDirectory, $newFileName);

        // Retournez le nom du fichier pour enregistrement dans la base de données
        return $newFileName;
    }
}
