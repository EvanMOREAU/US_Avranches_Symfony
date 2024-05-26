<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Equipe;
use App\Form\UserType;
use App\Entity\Category;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use App\Repository\TestsRepository;
use App\Repository\HeightRepository;
use App\Repository\WeightRepository;
use App\Services\ImageUploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\UserVerificationService;
use App\Services\HeightVerificationService;
use App\Services\WeightVerificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
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
  
    #[Route('/poste/set-poste-principal/{id}', name: 'app_set_poste_principal')]
    public function setPostePrincipal(user $user, Request $request, LoggerInterface $logger, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_PLAYER')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        $logger->debug('setPostePrincipal() user->getFirstname() = ' . $user->getFirstname());

        // Récupérez les données de la requête AJAX
        if(isset($_POST["postePrincipal"])){
            $postePrincipal = $_POST["postePrincipal"];
        }else{
            $postePrincipal = '';
        }
        $logger->debug('setPostePrincipal() postePrincipal = ' . $postePrincipal);

        // Mettez à jour l'entité user
        $user->setPostePrincipal($postePrincipal);

        $entityManager->persist($user);
        $entityManager->flush();
    }

    #[Route('/poste/set-poste-secondaire/{id}', name: 'app_set_poste_secondaire')]
    public function setPosteSecondaire(user $user, Request $request, LoggerInterface $logger, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_PLAYER')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        $logger->debug('setPosteSecondaire() user->getFirstname() = ' . $user->getFirstname());

        // Récupérez les données de la requête AJAX
        if(isset($_POST["posteSecondaire"])){
            $posteSecondaire = $_POST["posteSecondaire"];
        }else{
            $posteSecondaire = '';
        }
        $logger->debug('setPosteSecondaire() posteSecondaire = ' . $posteSecondaire);

        // Mettez à jour l'entité user
        $user->setPosteSecondaire($posteSecondaire);

        $entityManager->persist($user);
        $entityManager->flush();
    }
   

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'location' => 'n',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository, ImageUploaderHelper $imageUploaderHelper, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }
        
        // Récupérer la date de naissance du joueur
        $dateNaissance = $user->getDateNaissance();

        // Extrayez l'année de la date de naissance
        $anneeNaissance = $dateNaissance->format('Y');

        // Recherchez dans les catégories celle qui correspond à l'année de naissance
        $categoryRepository = $entityManager->getRepository(Category::class);
        $category = $categoryRepository->findOneBy(['name' => $anneeNaissance]);
            
        $formOptions = [];
        if ($category !== null) {
            // If category exists, add it to the form options
            $formOptions['category'] = $category->getId();
        }
        
        $form = $this->createForm(UserType::class, $user, $formOptions);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            if (!empty($plainPassword)) {
                $encodedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($encodedPassword);
            }

            $profimg = $form->get('profile_image')->getData();
            if (isset($profimg)) {
                $errorMessage = $imageUploaderHelper->uploadImage($form, $user);
                if (!empty($errorMessage)) {
                    $this->addFlash('danger', 'Une erreur s\'est produite : ' . $errorMessage);
                }
                $userRepository->save($user, true);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'location' => 'n',
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager, WeightRepository $weightRepository, HeightRepository $heightRepository, TestsRepository $testsRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {

            // foreach($user->getWeights() as $Weight){
            //     $user->removeWeight($Weight);
            // }
            $testsRepository->removeByUser($user);
            $heightRepository->removeByUser($user);
            $weightRepository->removeByUser($user);
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/poste-cache', name: 'app_user_cacheposte', methods: ['GET'])]
    public function poste_cache(user $user, LoggerInterface $logger): Response
    {
        if (!$this->isGranted('ROLE_PLAYER')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        $userVerif = $this->userVerificationService->verifyUser();
        $heightVerif = $this->heightVerificationService->verifyHeight();
        $weightVerif = $this->weightVerificationService->verifyWeight();
        if($userVerif == 0 ){return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);}
        else if($userVerif == -1) {return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);} 
        else if($userVerif == 1) {
            if($heightVerif == -1){return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);}
            else if($heightVerif == 0){return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);}
            else if($heightVerif == 1){
                if($weightVerif == -1){return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);}
                else if($weightVerif == 0){return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);}
                else if($weightVerif == 1){
                    return $this->render('user/hiddenposte.html.twig', [
                        'user' => $user,
                        'location' => 'e',
                    ]);
                }
            }
        }
    }

    #[Route('/{id}/poste', name: 'app_user_poste', methods: ['GET'])]
    public function poste(user $user, LoggerInterface $logger): Response
    {
        if (!$this->isGranted('ROLE_PLAYER')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }
        $userVerif = $this->userVerificationService->verifyUser();
        $heightVerif = $this->heightVerificationService->verifyHeight();
        $weightVerif = $this->weightVerificationService->verifyWeight();
        if($userVerif == 0 ){return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);}
        else if($userVerif == -1) {return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);} 
        else if($userVerif == 1) {
            if($heightVerif == -1){return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);}
            else if($heightVerif == 0){return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);}
            else if($heightVerif == 1){
                if($weightVerif == -1){return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);}
                else if($weightVerif == 0){return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);}
                else if($weightVerif == 1){
                    return $this->render('user/poste.html.twig', [
                        'user' => $user,
                        'location' => 'e',
                    ]);
                }
            }
        }
    }


    #[Route('/poste/poste-coach', name: 'app_user_coach', methods: ['GET'])]
    public function poste_coach(EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw $this->createAccessDeniedException('Access denied.');
        }
        
        $users = $entityManager->getRepository(user::class)->findAll();
        $equipes = $entityManager->getRepository(Equipe::class)->findAll();

        return $this->render('user/coachposte.html.twig', [
            'users' => $users,
            'equipes' => $equipes,
            'location' => 'e',
        ]);
    }

    #[Route('/poste/set-poste-cache-x/{id}', name: 'app_set_poste_cache_x')]
    public function setPosteCacheX(user $user, Request $request, LoggerInterface $logger, EntityManagerInterface $entityManager): Response
    {
        $logger->debug('setPosteCacheX() user->getFirstname() = ' . $user->getFirstname());

        // Récupérez les données de la requête AJAX
        $coord = $_POST["coordX"];
        $logger->debug('setPosteCoordX() PosteCoordX = ' . $coord);

        // Mettez à jour l'entité user
        $user->setPosteCoordX($coord);

        $entityManager->persist($user);
        $entityManager->flush();
    }

    #[Route('/poste/set-poste-cache-y/{id}', name: 'app_set_poste_cache_y')]
    public function setPosteCacheY(user $user, Request $request, LoggerInterface $logger, EntityManagerInterface $entityManager): Response
    {
        $logger->debug('setPosteCacheY() user->getFirstname() = ' . $user->getFirstname());

        // Récupérez les données de la requête AJAX
        $coord = $_POST["coordY"];
        $logger->debug('setPosteCoordY() PosteCoordY = ' . $coord);

        // Mettez à jour l'entité user
        $user->setPosteCordY($coord);

        $entityManager->persist($user);
        $entityManager->flush();
    }

    //////////////////////////////
}
