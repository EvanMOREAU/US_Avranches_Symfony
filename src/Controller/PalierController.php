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
    public function index(PalierRepository $palierRepository, UserRepository $userRepository): Response
    {
        // Récupérer tous les paliers
        $paliers = $palierRepository->findAll();

        // Tableau pour stocker le nombre de vidéos restantes à valider pour chaque palier
        $videosToValidate = [];

        // Parcourir chaque palier
        foreach ($paliers as $palier) {
            // Appeler la méthode countVideosByPalier pour chaque palier
            $videosToValidate[$palier->getId()] = $this->countVideosByPalier($palier);
        }

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->render('palier/index_admin.html.twig', [
                'paliers' => $paliers,
                'users' => $userRepository->findAll(),
                'videosToValidate' => $videosToValidate, // Passer le tableau des vidéos restantes à valider au template Twig
                'location' => 'd',
            ]);
        } else {
            return $this->render('palier/index_user.html.twig', [
                'paliers' => $paliers,
                'location' => 'd',
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
            'location' => 'd',
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
            'location' => 'd',
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
            'location' => 'd',
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

    
    #[Route('/users/{palierNumero}', name: 'app_validation', methods: ['GET', 'POST'])]
    public function validation(Request $request, $palierNumero, UserRepository $userRepository, PalierRepository $palierRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le palier correspondant au numéro donné
        $palier = $palierRepository->findOneBy(['numero' => $palierNumero]);

        // Vérifier si le palier existe
        if (!$palier) {
            throw $this->createNotFoundException('Palier non trouvé');
        }

        // Récupérer les utilisateurs associés à ce palier
        $users = $palier->getUsers();

        // Si la requête est de type POST, cela signifie qu'un utilisateur a été validé
        if ($request->isMethod('POST')) {
            // Récupérer le nom d'utilisateur à partir de la requête
            $username = $request->request->get('username');

            // Récupérer l'utilisateur correspondant au nom d'utilisateur
            $user = $userRepository->findOneBy(['username' => $username]);

            // Vérifier si l'utilisateur existe
            if (!$user) {
                throw $this->createNotFoundException('Utilisateur non trouvé');
            }

            // Récupérer le chemin de la vidéo à supprimer
            $videoPath = 'uploads/videos/' . $user->getUsername() . '_palier.mp4';

            // Vérifier si le fichier vidéo existe
            if (file_exists($videoPath)) {
                // Supprimer la vidéo du répertoire
                unlink($videoPath);
            }

            // Récupérer le palier suivant
            $nextPalier = $palierRepository->findOneBy(['numero' => $palierNumero + 1]);

            // Vérifier si le palier suivant existe
            if (!$nextPalier) {
                throw $this->createNotFoundException('Palier suivant non trouvé');
            }

            // Associer le palier suivant à l'utilisateur
            $user->setPalier($nextPalier);

            // Enregistrer les modifications dans la base de données
            $entityManager->flush();

            // Rediriger vers la page de validation avec le même palier
            return $this->redirectToRoute('app_validation', ['palierNumero' => $palierNumero]);
        }

        // Créez un tableau pour stocker les informations de la vidéo de chaque utilisateur
        $videos = [];

        // Parcourez chaque utilisateur pour récupérer la vidéo s'il existe
        foreach ($users as $user) {
            // Construire le chemin de la vidéo
            $videoPath = 'uploads/videos/' . $user->getUsername() . '_palier.mp4';

            // Vérifiez si le fichier vidéo existe
            if (file_exists($videoPath)) {
                // Ajoutez le chemin de la vidéo au tableau
                $videos[$user->getUsername()] = $videoPath;
            }
        }

        return $this->render('palier/validation.html.twig', [
            'users' => $users,
            'videos' => $videos, // Passez le tableau des vidéos à la vue
            'palierNumero' => $palierNumero,
            'location' => 'd',
        ]);
    }

    private function countVideos(): int
    {
        // Chemin du dossier contenant les vidéos
        $videoDirectory = 'uploads/videos/';

        // Compteur pour stocker le nombre total de vidéos
        $videoCount = 0;

        // Vérifiez si le dossier existe
        if (file_exists($videoDirectory)) {
            // Comptez les fichiers dans le dossier (à l'exclusion des répertoires)
            $videoCount = count(glob($videoDirectory . '/*.{mp4}', GLOB_BRACE));
        }

        // Retournez le nombre total de vidéos
        return $videoCount;
    }
    private function countVideosByPalier(Palier $palier): int
    {
        // Chemin du dossier contenant les vidéos
        $videoDirectory = 'uploads/videos/';

        // Compteur pour stocker le nombre de vidéos restantes à valider pour ce palier
        $videoCount = 0;

        // Récupérer les utilisateurs associés à ce palier
        $users = $palier->getUsers();

        // Parcourez chaque utilisateur pour vérifier s'ils ont téléchargé une vidéo pour ce palier
        foreach ($users as $user) {
            // Vérifiez si l'utilisateur a le même palier que celui fourni en argument
            if ($user->getPalier() === $palier) {
                // Construire le nom de fichier pour la vidéo de ce palier et cet utilisateur
                $videoName = $user->getUsername() . '_palier';

                // Construire le chemin de la vidéo
                $videoPath = $videoDirectory . '/' . $videoName . '.mp4';

                // Vérifiez si le fichier vidéo existe
                if (file_exists($videoPath)) {
                    // S'il n'existe pas, incrémentez le compteur
                    $videoCount++;
                }
            }
        }

        // Retournez le nombre total de vidéos restantes à valider pour ce palier
        return $videoCount;
    }

}
