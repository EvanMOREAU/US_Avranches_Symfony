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

        // if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
        //     // Redirection vers la page de connexion (app_login)
        //     return $this->redirectToRoute('app_login');
        // }

        if($this->userVerificationService->verifyUser() == 0 ){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }
        elseif($this->userVerificationService->verifyUser() == -1) {
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        } elseif($this->userVerificationService->verifyUser() == 1) {
            return $this->render('base.html.twig', [
                'controller_name' => 'DefaultController',
            ]);
        }


    }
}
