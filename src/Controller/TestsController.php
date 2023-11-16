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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Annotation\IsGranted;


#[Route('/tests')]
class TestsController extends AbstractController
{
    #[Route('/', name: 'app_tests_index')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        // Récupérez l'utilisateur actuellement connecté
        $user = $this->getUser();

        // Déclarez la variable selectedUserId et initialisez-la à null
        $selectedUserId = null;

        // Récupérez l'ID de l'utilisateur à partir des paramètres de l'URL
        $selectedUserId = $request->query->get('userId');

        // Vérifiez si l'ID de l'utilisateur sélectionné existe
        if ($selectedUserId && $this->isGranted('ROLE_SUPER_ADMIN')) {
            // Récupérez l'utilisateur correspondant à l'ID sélectionné
            $selectedUser = $userRepository->find($selectedUserId);

            // Utilisez la méthode getTests() pour récupérer tous les tests associés à l'utilisateur sélectionné
            $tests = $selectedUser ? $selectedUser->getTests() : [];
        } else {
            // Si aucun ID d'utilisateur sélectionné ou si l'utilisateur n'est pas superadmin, utilisez les tests de l'utilisateur connecté
            $tests = $user ? $user->getTests() : [];
        }

        return $this->render('tests/index.html.twig', [
            'controller_name' => 'TestsController',
            'tests' => $tests,
            'users' => $userRepository->findAll(),
            'user' => $user,
            'selectedUserId' => $selectedUserId,
        ]);
    }
    
    #[Route('/new', name: 'app_tests_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TestsRepository $testsRepository): Response
    {
        $test = new Tests();
        $form = $this->createForm(TestsFormType::class, $test);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();

            // Assurez-vous de récupérer correctement l'utilisateur actuellement connecté
            $user = $this->getUser();

            if (!$user) {
                // Gérer le cas où l'utilisateur n'est pas connecté
                throw new AccessDeniedException('Vous devez être connecté pour créer un test.');
            }

            // Associer le test à l'utilisateur connecté
            $test->setUser($user);
            $test->setDate(new \DateTime());

            // Enregistrez le test dans la base de données
            $testsRepository->save($test, true);

            $this->addFlash('success', 'Le test a été créé avec succès.');

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
}
