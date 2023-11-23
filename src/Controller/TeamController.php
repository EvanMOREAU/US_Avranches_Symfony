<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\UserVerificationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamController extends AbstractController
{
    private $userVerificationService;

    public function __construct(UserVerificationService $userVerificationService)
    {
        $this->userVerificationService = $userVerificationService;
    }
    
    #[Route('/team', name: 'app_team')]
    public function index(UserRepository $userRepository): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('team/index.html.twig', [
            'controller_name' => 'TeamController',
            'users' => $userRepository->findAll(),

        ]);
    }
}
