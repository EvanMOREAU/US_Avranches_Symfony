<?php

namespace App\Controller;

use App\Form\VerifCodeType;
use App\Repository\PlayerCodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/verifcode')]
class VerifCodeController extends AbstractController
{
    #[Route('/', name: 'app_verif_code', methods: ['GET','POST'])]
    public function index(PlayerCodeRepository $playerCodeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();
        $form = $this->createForm(VerifCodeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $playerCode = $playerCodeRepository->findCurrentCode();
            $code = $playerCode->getCode();
            $sentCode = $form->getData();
            // dump($sentCode);
            if( $code == $sentCode){
                $user->setIsCodeValidated(true);
                $entityManager->flush();
                return $this->redirectToRoute('app_default', [], Response::HTTP_SEE_OTHER);
            }else{
                
            }
        }
        return $this->renderForm('verif_code/index.html.twig', [
            'form' => $form,
            'location' => '',
        ]);
    }
}
