<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConditionUtilisationController extends AbstractController
{
    #[Route('/cu', name: 'app_condition_utilisation')]
    public function index(): Response
    {
        return $this->render('condition_utilisation/index.html.twig', [
            'controller_name' => 'ConditionUtilisationController',
            'location' => '',
        ]);
    }
}
