<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestsController extends AbstractController
{
    #[Route('/tests', name: 'app_tests')]
    public function index(): Response
    {
        return $this->render('tests/index.html.twig', [
            'controller_name' => 'TestsController',
        ]);
    }
}
