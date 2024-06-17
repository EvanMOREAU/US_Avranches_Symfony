<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\RegistrationA2F;
use App\Form\RegistrationFormType;
use App\Repository\PalierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, PalierRepository $palierRepository): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Récupérer le code d'inscription
            $registrationCode = $form->get('registrationCode')->getData();

            // Vérifier si le code est valide
            $a2fCode = $entityManager->getRepository(RegistrationA2F::class)->findOneBy(['code' => $registrationCode]);

            if (!$a2fCode) {
                // Ajouter un message d'erreur et réafficher le formulaire
                $this->addFlash('error', 'Le code d\'inscription est invalide.');
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            // encode the plain password
            $user->setPassword(
                    $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $palier = $palierRepository->findLowestIdPalier();
            if ($palier) {
                $user->setPalier($palier);
            } else {
                // Handle the case where no Palier is found
                throw new \Exception('No Palier found');
            }
            $user->setRoles([
                "ROLE_PLAYER",
            ]);
            $user->setPalierEnded(false);
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_default');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
