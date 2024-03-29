<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CUController extends AbstractController
{
    #[Route('/conditions-utilisation', name: 'app_condiutil')]
    public function index(): Response
    {
        return $this->render('condition_utilisation/index.html.twig', [
            'controller_name' => 'CUController',
            'location' => '',
        ]);
    }
}
