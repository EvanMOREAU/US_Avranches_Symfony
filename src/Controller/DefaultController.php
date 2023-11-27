<?php

namespace App\Controller;

use App\Service\UserVerificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
    private $userVerificationService;

    public function __construct(UserVerificationService $userVerificationService)
    {
        $this->userVerificationService = $userVerificationService;
    }

    #[Route('/', name: 'app_default')]
    public function index(): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('login/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
