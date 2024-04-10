<?php

namespace App\Controller;

use App\Entity\PlayerCode;
use App\Form\PlayerCodeType;
use App\Repository\PlayerCodeRepository;
use App\Service\UserVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/playercode')]
class PlayerCodeController extends AbstractController
{
    private $userVerificationService;

    public function __construct(UserVerificationService $userVerificationService)
    {
        $this->userVerificationService = $userVerificationService;
    }
    
    #[Route('/', name: 'app_player_code_index', methods: ['GET'])]
    public function index(PlayerCodeRepository $playerCodeRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }

        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('player_code/index.html.twig', [
            'player_codes' => $playerCodeRepository->findAll(),
            'location' => 'm',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_player_code_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PlayerCode $playerCode, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }
        
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(PlayerCodeType::class, $playerCode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_player_code_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('player_code/edit.html.twig', [
            'player_code' => $playerCode,
            'form' => $form,
            'location' => 'm',

        ]);
    }
}
