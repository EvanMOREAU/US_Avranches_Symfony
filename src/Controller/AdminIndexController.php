<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminIndexController extends AbstractController
{
    #[Route('/admin/index', name: 'app_admin_index')]
    public function index(): Response
    {
        return $this->render('admin_index/index.html.twig', [
            'controller_name' => 'AdminIndexController',
        ]);
    }
}
