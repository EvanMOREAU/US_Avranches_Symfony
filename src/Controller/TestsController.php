<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Tests;
use App\Form\DataTransformer\CooperTimeTransformer;
use App\Repository\UserRepository;
use App\Form\TestsFormType;
use Doctrine\ORM\EntityManager;
use App\Repository\TestsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Annotation\IsGranted;


#[Route('/tests')]
class TestsController extends AbstractController
{
    #[Route('/', name: 'app_tests_index')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
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

        usort($testsArray, function ($a, $b) use ($order) {
            if ($order === 'asc') {
                return $a->getDate() <=> $b->getDate();
            } else {
                return $b->getDate() <=> $a->getDate();
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
    public function new(Request $request, TestsRepository $testsRepository, UserRepository $userRepository): Response
    {
        $test = new Tests();
        
        // Assurez-vous que le champ user n'est pas requis
        // $test->setUser($this->getUser()); // Ne pas définir l'utilisateur ici

        $form = $this->createForm(TestsFormType::class, $test);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            
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
    #[Route('/{id}/delete', name: 'app_tests_delete', methods: ['GET'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, TestsRepository $testsRepository, $id): Response
    {
        $test = $testsRepository->find($id);

        if (!$test) {
            throw $this->createNotFoundException('Test non trouvé');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($test);
        $entityManager->flush();

        // Ajoutez un message flash pour la suppression réussie
        $this->addFlash('success', 'La suppression a été réalisée avec succès.');

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
}
