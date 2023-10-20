<?php

namespace App\Controller;

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
    public function index(TestsRepository $TestsRepository, UserRepository $userRepository): Response
    {
        $tests = $TestsRepository->findAll();
        
        return $this->render('tests/index.html.twig', [
            'controller_name' => 'TestsController',
            'users' => $userRepository->findAll(),
            'tests' => $tests,
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
            $test->setDate(new \DateTime());
            
            $testsRepository->save($test, true);


            return $this->redirectToRoute('app_tests_index', [], Response::HTTP_SEE_OTHER);
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
