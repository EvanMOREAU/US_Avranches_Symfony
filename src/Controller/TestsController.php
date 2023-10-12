<?php

namespace App\Controller;

use App\Entity\Tests;
use App\Form\DataTransformer\CooperTimeTransformer;
use App\Form\TestsFormType;
use Doctrine\ORM\EntityManager;
use App\Repository\TestsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/tests')]
class TestsController extends AbstractController
{
    #[Route('/', name: 'app_tests_index')]
    public function index(TestsRepository $TestsRepository): Response
    {
        $tests = $TestsRepository->findAll();
        
        return $this->render('tests/index.html.twig', [
            'controller_name' => 'TestsController',
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

    #[Route('/{id}/edit', name: 'app_tests_edit', methods: ['GET', 'POST'])]
    function edit(Request $request, TestsRepository $testsRepository, $id): Response
    {
        $test = $testsRepository->find($id); // Récupérez l'entité Tests en fonction de l'ID

        if (!$test) {
            throw $this->createNotFoundException('Test non trouvé');
        }

        $form = $this->createForm(TestsFormType::class, $test); // Lier le formulaire aux données de l'entité

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $testsRepository->save($test, true); // Utilisez l'entité $test au lieu du formulaire

            // Redirigez l'utilisateur ou effectuez d'autres actions ici
        }

        return $this->renderForm('tests/edit.html.twig', [
            'tests' => $test, // Utilisez $test au lieu de $tests
            'form' => $form,
        ]);
    }
}
