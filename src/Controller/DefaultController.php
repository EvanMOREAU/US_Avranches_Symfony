<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\TestsRepository;
use App\Repository\PalierRepository;
use App\Repository\WeightRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\UserVerificationService;
use App\Services\HeightVerificationService;
use App\Services\WeightVerificationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
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
    public function index(TestsRepository $testsRepository, UserRepository $userRepository, PalierRepository $palierRepository, WeightRepository $weightRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

         $userVerif = $this->userVerificationService->verifyUser();
        $heightVerif = $this->heightVerificationService->verifyHeight();
        $weightVerif = $this->weightVerificationService->verifyWeight();

        $user = $this->getUser();
        if ($user) {
            $user->setLastConnection(new \DateTime());
            $entityManager->flush();
            $playerId = $user->getId();
            // $playerElementsCount = $testsRepository->countPlayerElements($playerId);
            $equipe = $user->getEquipe();
            $currentPalierNumber =  $user->getPalier()->getNumero();
            if($equipe){
                $usersInSameTeam = $userRepository->findBy(['equipe' => $equipe]);
                $countUsersInSameTeam = $userRepository->count(['equipe' => $equipe]);
            }else{
                $usersInSameTeam = [];
                $countUsersInSameTeam = 0;
            }
            $latestWeightDate = $weightRepository->getLatestWeightDate($playerId);
            // Récupérer tous les utilisateurs
            $users = $userRepository->findAll();

            // Initialiser un tableau pour stocker les utilisateurs ayant le rôle "ROLE_COACH"
            $coachUsers = [];

            // Parcourir tous les utilisateurs
            foreach ($users as $user) {
                // Vérifier si l'utilisateur a le rôle "ROLE_COACH"
                if (in_array('ROLE_COACH', $user->getRoles())) {
                    // Ajouter l'utilisateur au tableau des utilisateurs ayant le rôle "ROLE_COACH"
                    $coachUsers[] = $user;
                }
            }
        }else{
            return $this->render('/login/index.html.twig', ['controller_name' => 'SecurityController','location' => 'a',]);
        }
        if($userVerif == 0 ){return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);}
        else if($userVerif == -1) {return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);} 
        else if($userVerif == 1) {
            if($heightVerif == -1){return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);}
            else if($heightVerif == 0){return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);}
            else if($heightVerif == 1){
                if($weightVerif == -1){return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);}
                else if($weightVerif == 0){return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);}
                else if($weightVerif == 1){
                    return $this->render('base.html.twig', [
                        'location' => 'a',
                        'controller_name' => 'DefaultController',
                        'testcount' => $testsRepository->count([]),
                        'addedtest' => $testsRepository->countTestsAddedThisMonth($playerId),
                        'category' => $user->getCategory(),
                        'usercount' => $userRepository->count([]),
                        'equipeuser' => $usersInSameTeam,
                        'equipeusercount' => $countUsersInSameTeam,
                        'paliers' => $palierRepository->findAll(),
                        'weightIn' => $latestWeightDate,
                        'coachUsers' => $coachUsers,
                    ]);
                }
                return $this->render('base.html.twig', [
                    'controller_name' => 'DefaultController',
                    'location' => 'a',
                    
                ]);
            }
        }
    }
}
