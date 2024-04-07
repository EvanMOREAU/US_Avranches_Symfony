<?php

namespace App\Controller;

use PHPlot\PHPlot;
use App\Entity\Pdf;
use App\Entity\User;
use App\Entity\Tests;
use App\Entity\Height;
use App\Entity\Weight;
use App\Repository\UserRepository;
use App\Repository\TestsRepository;
use App\Service\UserVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\HeightVerificationService;
use App\Service\WeightVerificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ChartConfigurationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[Route('/pdf', name: 'app_pdf')]
class PdfController extends AbstractController
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

    #[Route('/', name: 'app_pdf_index')]
    public function pdf(Request $request, UserRepository $userRepository, TestsRepository $testsRepository, ChartConfigurationRepository $chartConfigurationRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'ID de l'utilisateur à partir de la route
        $userId = $request->attributes->get('userId');
    
        // Vérifier le rôle de l'utilisateur
        if ($this->isGranted('ROLE_COACH') || $this->isGranted('ROLE_SUPER_ADMIN')) {
            // L'utilisateur a le rôle ROLE_COACH ou ROLE_SUPER_ADMIN, récupérer les données du joueur ciblé
            $user = $userRepository->find($userId);
    
            if (!$user) {
                throw $this->createNotFoundException('Utilisateur non trouvé');
            }
        } else {
            // Utilisateur ordinaire, utiliser les données de l'utilisateur actuellement connecté
            $token = $this->get('security.token_storage')->getToken();
    
            if (!$token) {
                // Rediriger vers une page d'erreur ou afficher un message d'erreur
                throw new \Exception('Token d\'authentification non trouvé');
            }
    
            $user = $token->getUser();
        }
    
        $pdf = new Pdf();
    
        if ($user !== null) {
            if ($user instanceof User) {
                $tests = $testsRepository->findBy(['user' => $user]);
    
                // Récupérer les tests triés par date décroissante
                $tests = $testsRepository->findBy(['user' => $user], ['date' => 'DESC']);
    
                // Configuration du PDF
                $pdf->SetAuthor('SIO TEAM ! 💻');
                $pdf->SetTitle('Fiche joueur');
                $pdf->SetFont('times', '', 14);
    
                // Ajout d'une nouvelle page
                $pdf->AddPage();
                $pdf->setJPEGQuality(75);
    
                // Calcul des dimensions de la page
                $largeurPage = $pdf->getPageWidth() + 30;
                $hauteurPage = $pdf->getPageHeight() - 25;
    
                // Configuration de la police et des couleurs
                $pdf->SetFont('helvetica', '', 20);
                $pdf->SetTextColor(0, 0, 0);
    
                // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
    
                // Ajout du nom du joueur
                $pdf->MultiCell(70, 10, $user->getFirstName() . ' ' . $user->getLastName(), 0, 'C', 0, 1, '0', '40', true);
    
                // Configuration de la police et des couleurs pour le contenu du joueur
                $pdf->SetFont('helvetica', 'B', 20);
                $pdf->SetTextColor(0, 0, 0);
    
                // Récupérer les données de taille et de poids associées à l'utilisateur
                $heights = $entityManager->getRepository(Height::class)->findBy(['user' => $user], ['date' => 'ASC']);
                $weights = $entityManager->getRepository(Weight::class)->findBy(['user' => $user], ['date' => 'ASC']);
    
                // Construction du contenu des tailles
                $contentHeights = '';
                foreach ($heights as $height) {
                    $contentHeights .= '<b>Taille :</b> ' . $height->getValue() . ' cm (' . $height->getDate()->format('d/m/Y') . ')<br>';
                    $contentHeights .= '<br>'; // Ajout d'un espace entre chaque ligne de taille
                }
    
                // Construction du contenu des poids
                $contentWeights = '';
                foreach ($weights as $weight) {
                    $contentWeights .= '<b>Poids :</b> ' . $weight->getValue() . ' kg (' . $weight->getDate()->format('d/m/Y') . ')<br>';
                    $contentWeights .= '<br>'; // Ajout d'un espace entre chaque ligne de poids
                }
    
                // Contenu du joueur (avec HTML)
                $contentInfos = '
                    <style>.link { color: rgb(42, 56, 114) }</style>
                    <br><br><br>
                    <b><i>  Informations du joueur : </i></b>
                    <br><br>
                    <p><b>Date de naissance : </b>' . $user->getDateNaissance()->format('d/m/Y') . '
                    <br><hr><br><div></div>
                    <b>Catégorie : </b>' . $user->getCategory() . '
                    <br><hr><br><div></div>
                    ' . $contentHeights . '
                    <br><hr><br><div></div>
                    ' . $contentWeights . '
                    </p>';
    
                // Ajout du contenu du joueur au PDF
                $pdf->SetFont('helvetica', '', 10);
                $pdf->writeHTMLCell(65, 230, '', '', $contentInfos, 0, 0, 0, true, '', true);
    
                // Déplacer le curseur vers le bas de la page
                $pdf->SetY($hauteurPage - 60); // Ajustez la valeur en fonction de la position souhaitée
                // Contenu du paragraphe "Contact"
                $contentContact = '
                    <p><b> Contact :</b><br>
                    <br> Christelle DELARUE<br>
                    <br>
                    Club House US Avranches MSM<br>
                    Allée Jacques Anquetil<br>
                    50300 Avranches.<br><br>
                    <b>Téléphone :</b> 02.33.48.30.78 <br><br>
                    <b>Mails :</b><br>
                    <span class="link"><u>communication@us-avranches.fr</u></span><br>
                    <span class="link"><u>partenaires@us-avranches.fr</u></span><br>
                    <span class="link"><u>us.avranches@orange.fr</u></span>
                    </p>';
    
                // Ajout du contenu du paragraphe "Contact" au PDF
                $pdf->writeHTMLCell(0, 0, '', '', $contentContact, 0, 1, 0, true, '', true);
    
                $profileImagePath = 'uploads/images/' . $user->getId() . '.jpg';
    
                if (file_exists($profileImagePath)) {
                    // Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
                    $pdf->Image($profileImagePath, 14, 50, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);
                } else {
                    // Utilisez une image anonyme
                    $pdf->Image('img/anonyme.jpg', 14, 50, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);
                }
    
                foreach ($tests as $test) {
                    // Ajout d'une nouvelle page pour chaque test
                    $pdf->AddPage();
                    $pdf->setJPEGQuality(75);
    
                    // Calcul des dimensions de la page
                    $largeurPage = $pdf->getPageWidth() + 30;
                    $hauteurPage = $pdf->getPageHeight() - 25;
    
                    $pdf->SetFontSize(16); // Définir la taille de police à 16 points (ajustez selon vos besoins)
                    $pdf->MultiCell(70, 10, $user->getFirstName() . ' ' . $user->getLastName(), 0, 'C', 0, 1, '', '', true);
                    $pdf->SetFontSize(10); // Rétablir la taille de police à la valeur par défaut (si nécessaire)
    
                    // --- Contenu du pdf ---
                    $contentTests = '<br><br><br>';
    
                    $contentTests .= '<br><hr><br><div></div>
                    <b>Taille :</b> 173 cm
                    <br><hr><br><div></div>
                    <p><b>VMA : </b>' . $test->getVma() . ' km/h 
                    <br><hr><br><div></div>
                    <b>Cooper : </b>' . $test->getCooper() . ' mètres
                    <br><hr><br><div></div>
                    <b>Demi-cooper : </b>' . $test->getDemiCooper() . ' mètres
                    <br><hr><br><div></div>
                    <b>Jongles pied gauche : </b>' . $test->getJongleGauche() . ' 
                    <br><hr><br><div></div>
                    <b>Jongles pied droit : </b>' . $test->getJongleDroit() . ' 
                    <br><hr><br><div></div>
                    <b>Jongles tête : </b>' . $test->getJongleTete() . ' 
                    <br><hr><br><div></div>
                    <b>Date des tests : </b>' . $test->getDate()->format('d/m/Y') . ' 
                    <br><hr><br><div></div>
                    <b>Conduite de balle : </b>' . $test->getConduiteBalle() . ' secondes
                    <br><hr><br><div></div>
                    <b>Vitesse : </b>' . $test->getVitesse() . ' secondes
                    </p>';
    
                    $pdf->writeHTMLCell(65, 230, '', '', $contentTests, 0, 0, 0, true, '', true);
                    // Ajout d'une image au PDF
                    $pdf->Image('img/graph_' . $user->getFirstName() . '.jpg', 95, 150, 100, 100, '', '', '', false, 300, '', false, false, 1, false, false, false);
    
                    // Insérer la date au dessus de l'image et faire en sorte qu'elle soit bien visible.
                    $posX = 145;
                    $posY = 33.3;
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->SetFontSize(20);
                    $pdf->MultiCell($posX, $posY, $test->getDate()->format('d/m/Y, H:i:s'), 0, 'C', 0, 1, '', '', true);
                    $pdf->SetFontSize(10);
                    $pdf->SetTextColor(0, 0, 0);
    
                    $pdf->Image('img/joueur.jpg', 130, $posY, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);
                }
    
                // Génération du PDF et envoi en réponse
                ob_clean(); // Efface la sortie tampon
                $pdfContent = $pdf->Output('US-Avranches-' . '.pdf', 'S');
    
                $response = new Response($pdfContent, Response::HTTP_OK, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="US-Avranches.pdf"',
                ]);
    
                return $response;
            }
        }
    
        // Gestion des cas d'erreur
        return new Response('Erreur');
    }

    #[Route('/list-players', name: 'app_pdf_list_players')]
    public function listPlayers(UserRepository $userRepository): Response
    {
        $userVerif = $this->userVerificationService->verifyUser();
        $heightVerif = $this->heightVerificationService->verifyHeight();
        $weightVerif = $this->weightVerificationService->verifyWeight();

        // Récupérez la liste des utilisateurs ayant le rôle ROLE_PLAYER
        $players = $userRepository->findByRole('ROLE_PLAYER');
        if ($userVerif == 0) {
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        } else if ($userVerif == -1) {
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        } else if ($userVerif == 1) {
            if ($heightVerif == -1) {
                return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);
            } else if ($heightVerif == 0) {
                return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);
            } else if ($heightVerif == 1) {
                if ($weightVerif == -1) {
                    return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);
                } else if ($weightVerif == 0) {
                    return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);
                } else if ($weightVerif == 1) {
                    // Affichez la liste des joueurs dans une vue
                    return $this->render('pdf/list.players.html.twig', [
                        'players' => $players,
                        'location' => 'f',
                    ]);
                }
            }
        }
    }


    #[Route('/{userId}', name: 'app_pdf_view_pdf')]
    public function viewPdf(int $userId, Request $request, UserRepository $userRepository, TestsRepository $testsRepository, ChartConfigurationRepository $chartConfigurationRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérez l'utilisateur
        $user = $userRepository->find($userId);

        // Vérifiez si l'utilisateur existe et a le rôle ROLE_PLAYER
        if (!$user || !in_array('ROLE_PLAYER', $user->getRoles(), true)) {
            // Redirigez vers une page d'erreur ou affichez un message d'erreur
            throw $this->createNotFoundException('Utilisateur non trouvé ou n\'a pas le rôle de joueur.');
        }

        // Créez une nouvelle instance de la classe Request avec les paramètres appropriés
        $request = new Request([], [], ['userId' => $userId]);

        // Appel de la méthode pdf avec le nouvel objet Request
        $pdfResponse = $this->pdf($request, $userRepository, $testsRepository, $chartConfigurationRepository, $entityManager, $userId);

        // Retournez la réponse du PDF
        return $pdfResponse;
    }

}