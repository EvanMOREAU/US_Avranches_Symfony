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
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('player_code/index.html.twig', [
            'player_codes' => $playerCodeRepository->findAll(),
            'location' => '',
        ]);
    }

    #[Route('/new', name: 'app_player_code_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $playerCode = new PlayerCode();
        $form = $this->createForm(PlayerCodeType::class, $playerCode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($playerCode);
            $entityManager->flush();

            return $this->redirectToRoute('app_player_code_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('player_code/new.html.twig', [
            'player_code' => $playerCode,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_player_code_show', methods: ['GET'])]
    public function show(PlayerCode $playerCode): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('player_code/show.html.twig', [
            'player_code' => $playerCode,
            'location' => '',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_player_code_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PlayerCode $playerCode, EntityManagerInterface $entityManager): Response
    {
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
        ]);
    }

    #[Route('/{id}', name: 'app_player_code_delete', methods: ['POST'])]
    public function delete(Request $request, PlayerCode $playerCode, EntityManagerInterface $entityManager): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$playerCode->getId(), $request->request->get('_token'))) {
            $entityManager->remove($playerCode);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_player_code_index', [], Response::HTTP_SEE_OTHER);
    }
}
