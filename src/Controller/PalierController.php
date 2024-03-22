<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Palier;
use App\Form\PalierType;
use App\Repository\UserRepository;
use App\Repository\PalierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Annotation\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


#[Route('/palier')]
class PalierController extends AbstractController
{

    #[Route('/', name: 'app_palier_index', methods: ['GET', 'POST'])]
    public function index(PalierRepository $palierRepository): Response
    {
        $paliers = $palierRepository->findBy([], ['numero' => 'ASC']);
        
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->render('palier/index_admin.html.twig', [
                'paliers' => $paliers,
            ]);
        } else {
            return $this->render('palier/index_user.html.twig', [
                'paliers' => $paliers,
            ]);
        }

    }
    #[Route('/new', name: 'app_palier_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        // Récupérer le numéro passé en paramètre
        $numero = $request->query->getInt('numero', 0);

        // Vérifier si le numéro existe déjà
        $existingPalier = $entityManager->getRepository(Palier::class)->findOneBy(['numero' => $numero]);

        if ($existingPalier) {
            // Numéro existant, rediriger vers la page de création avec un nouveau numéro
            return $this->redirectToRoute('app_palier_new', ['numero' => $numero + 1]);
        }

        // Le numéro est unique, procéder à la création du palier
        $palier = new Palier();
        $palier->setNumero($numero);

        $form = $this->createForm(PalierType::class, $palier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($palier);
            $entityManager->flush();

            return $this->redirectToRoute('app_palier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('palier/new.html.twig', [
            'palier' => $palier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_palier_show', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function show(Palier $palier): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return $this->render('palier/show.html.twig', [
            'palier' => $palier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_palier_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function edit(Request $request, Palier $palier, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(PalierType::class, $palier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_palier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('palier/edit.html.twig', [
            'palier' => $palier,
            'form' => $form,
        ]);
    }



    #[Route('/palier/{id}', name: 'app_palier_delete', methods: ['POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, Palier $palier, EntityManagerInterface $entityManager): Response

    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $response = ['success' => false];

        if ($this->isCsrfTokenValid('delete' . $palier->getId(), $request->request->get('_token'))) {

            $entityManager->remove($palier);
            $entityManager->flush();

            $response['success'] = true;
        }

        return $this->redirectToRoute('app_palier_index'); // Redirigez où vous voulez après la suppression
    }
    private function uploadVideo(UploadedFile $videoFile, string $videoName): string
    {
        // Définissez le répertoire où vous souhaitez stocker les vidéos
        $videoDirectory = $this->getParameter('upload_dir');

        // Utilisez le nom fourni dans le formulaire avec une extension "mp4"
        $newFileName = $videoName . '.mp4';

        // Déplacez le fichier dans le répertoire configuré
        $videoFile->move($videoDirectory, $newFileName);

        // Retournez le nom du fichier pour enregistrement dans la base de données
        return $newFileName;
    }

    #[Route('uploads/videos', name: 'upload_video', methods: ['POST'])]
    public function handleVideoUpload(Request $request): Response
    {
        // Récupérer le fichier vidéo téléchargé à partir de la requête
        $videoFile = $request->files->get('video');

        $userUsername = $request->request->get('user_username');

        // Construire le nom de fichier pour la vidéo
        $videoName = $userUsername . '_palier';

        // Appelez la méthode uploadVideo pour gérer le téléversement de la vidéo
        $uploadedFileName = $this->uploadVideo($videoFile, $videoName);

        // Faites tout autre traitement nécessaire, par exemple, enregistrez le chemin du fichier dans la base de données

        // Redirigez où vous le souhaitez après le téléchargement
        return $this->redirectToRoute('app_palier_index');
    }

    
    #[Route('/users/{palierNumero}', name: 'app_validation', methods: ['GET'])]
    public function validation(Request $request, $palierNumero, UserRepository $userRepository): Response
    {
        $users = $userRepository->findUsersByPalier($palierNumero);

        return $this->render('validation.html.twig', [
            'users' => $users,
        ]);

    }
}
