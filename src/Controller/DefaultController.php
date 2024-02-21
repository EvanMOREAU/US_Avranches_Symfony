<?php

namespace App\Controller;

use App\Service\UserVerificationService;
use App\Service\WeightVerificationService;
use App\Service\HeightVerificationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class DefaultController extends AbstractController
{
    private $userVerificationService;
    private $heightVerificationService;
    private $weightVerificationService;

    public function __construct(UserVerificationService $userVerificationService, HeightVerificationService $heightVerificationService, WeightVerificationService $weightVerificationService)
    {
        $this->userVerificationService = $userVerificationService;
        $this->heightVerificationService = $heightVerificationService;
        $this->weightVerificationService = $weightVerificationService; 
    }

    #[Route('/', name: 'app_default')]
    public function index(): Response
    {
        $userVerif = $this->userVerificationService->verifyUser();
        $heightVerif = $this->heightVerificationService->verifyHeight();
        $weightVerif = $this->weightVerificationService->verifyWeight();

        $user = $this->getUser();
        if ($user) {
            $user->setLastConnection(new \DateTime());
            $this->getDoctrine()->getManager()->flush();
        }

        if($userVerif == 0 ){return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);}
        else if($userVerif == -1) {return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);} 
        else if($userVerif == 1) {
            if($heightVerif == -1){return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);}
            else if($heightVerif == 0){return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);}
            else if($heightVerif == 1){
                if($weightVerif == -1){return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);}
                else if($weightVerif == 0){return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);}
                else if($weightVerif == 1){return $this->render('base.html.twig', ['controller_name' => 'DefaultController',]);}
                // return $this->render('base.html.twig', ['controller_name' => 'DefaultController',]);
            }
        }

    }
}
