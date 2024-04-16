<?php

namespace App\Controller;

use App\Entity\Pdf;
use App\Entity\User;
use App\Entity\Player;
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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/pdf', name: 'app_pdf')]
class PdfController extends AbstractController
{
    private $userVerificationService;
    private $heightVerificationService;
    private $weightVerificationService;
    private $tokenStorage;

    public function __construct(UserVerificationService $userVerificationService, HeightVerificationService $heightVerificationService, WeightVerificationService $weightVerificationService, TokenStorageInterface $tokenStorage)
    {
        $this->userVerificationService = $userVerificationService;
        $this->heightVerificationService = $heightVerificationService;
        $this->weightVerificationService = $weightVerificationService;
        $this->tokenStorage = $tokenStorage;

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
            $user = $this->getUser();
            if (!$user) {
                // Rediriger vers une page d'erreur ou afficher un message d'erreur
                throw new \Exception('Token d\'authentification non trouvé');
            }
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

                $pdf->SetX(10); // Définir la position X pour les informations du joueur
                // Configuration de la police et des couleurs
                $pdf->SetFont('helvetica', '', 20);
                $pdf->SetTextColor(0, 0, 0);

                $pdf->MultiCell(70, 10, $user->getFirstName() . ' ' . $user->getLastName(), 0, 'C', 0, 1, '10', '45', true);
                $pdf->SetFont('helvetica', '', 15);

                $pdf->MultiCell(70, 0, $user->getDateNaissance()->format('d/m/Y') . ' ' . '(' . $user->getCategory() . ')', 0, 'C', 0, 1, '10', '55', true);

                // Configuration de la police et des couleurs pour le contenu du joueur
                $pdf->SetFont('helvetica', 'B', 20);
                $pdf->SetTextColor(0, 0, 0);

                // Récupérer les données de taille et de poids associées à l'utilisateur
                $heights = $entityManager->getRepository(Height::class)->findBy(['user' => $user], ['date' => 'ASC']);
                $weights = $entityManager->getRepository(Weight::class)->findBy(['user' => $user], ['date' => 'ASC']);

                // Construction du contenu des tailles
                $contentHeights = '';
                $lastFiveHeights = array_slice($heights, -3); // Obtenir les 5 dernières tailles
                foreach ($lastFiveHeights as $height) {
                    $contentHeights .= '<b>Taille :</b> ' . $height->getValue() . ' cm (' . $height->getDate()->format('d/m/Y') . ')<br>';
                    $contentHeights .= '<br>'; // Ajout d'un espace entre chaque ligne de taille
                }

                $pdf->SetFont('helvetica', 'B', 20);
                $pdf->SetTextColor(0, 0, 0);

                // Construction du contenu des poids
                $contentWeights = '';
                $lastFiveWeights = array_slice($weights, -3); // Obtenir les 5 derniers poids
                foreach ($lastFiveWeights as $weight) {
                    $contentWeights .= '<b>Poids :</b> ' . $weight->getValue() . ' kg (' . $weight->getDate()->format('d/m/Y') . ')<br>';
                    $contentWeights .= '<br>'; // Ajout d'un espace entre chaque ligne de poids
                }

                // Contenu du joueur (avec HTML)
                $contentInfos = '
                    <style>.link { color: rgb(42, 56, 114) }</style>
                    <br><br><br><br><br><br><br><br><br><br><br><br>
                    <b><i>Informations du joueur : </i></b>
                    <br><hr><br><div></div><div></div><div></div>
                    <u>Email de contact</u> : ' . $user->getEmail() . '
                    </p>';

                // Ajout du contenu du joueur au PDF
                $pdf->SetFont('helvetica', '', 10);
                $pdf->writeHTMLCell(70, 230, '', '', $contentInfos, 0, 0, 0, true, '', true);

                // Déplacer le curseur pour afficher le tableau
                $pdf->SetY(150);
                $pdf->SetX(110);
                $pdf->SetFont('helvetica', '', 12);

                // Définir les largeurs des colonnes
                $heightColumnWidth = 140; // Largeur de la colonne des tailles
                $weightColumnWidth = 140; // Largeur de la colonne des poids

                // Créer le tableau HTML
                $htmlTable = '
                <table border="0" cellpadding="6">
                    <thead>
                        <tr align="center">
                            <th width="' . $heightColumnWidth . '"><b>Tailles</b></th>
                            <th width="' . $weightColumnWidth . '"><b>Poids</b></th>
                        </tr>
                    </thead>
                    <tbody>';

                // Vérifier s'il y a des données disponibles
                if (!empty($lastFiveHeights) && !empty($lastFiveWeights)) {
                    // Compteur pour limiter l'affichage à un maximum de trois données
                    $count = 0;

                    // Ajouter les données des tailles et des poids dans le tableau
                    foreach ($lastFiveHeights as $index => $height) {
                        if ($count >= 3) {
                            break; // Arrêter la boucle une fois que trois données ont été ajoutées
                        }

                        $weight = isset($lastFiveWeights[$index]) ? $lastFiveWeights[$index] : null; // Récupérer le poids correspondant
                        $htmlTable .= '
                        <tr align="center">
                            <td width="' . $heightColumnWidth . '">' . $height->getValue() . ' cm (' . $height->getDate()->format('d/m/Y') . ')</td>';
                        if ($weight) {
                            $htmlTable .= '<td width="' . $weightColumnWidth . '">' . $weight->getValue() . ' kg (' . $weight->getDate()->format('d/m/Y') . ')</td>';
                        } else {
                            $htmlTable .= '<td width="' . $weightColumnWidth . '">-</td>'; // Afficher un tiret si aucune donnée de poids n'est disponible
                        }
                        $htmlTable .= '
                        </tr>';

                        $count++; // Incrémenter le compteur
                    }
                } else {
                    // Afficher un message indiquant qu'il n'y a pas de données disponibles
                    $htmlTable .= '
                    <tr align="center">
                        <td colspan="2">Aucune donnée disponible</td>
                    </tr>';
                }

                $htmlTable .= '
                </tbody>
                </table>';

                // Afficher le tableau HTML dans le PDF
                $pdf->writeHTML($htmlTable, true, false, false, false, '');

                // Déplacer le curseur vers le bas de la page
                $pdf->SetY($hauteurPage - 80); // Ajustez la valeur en fonction de la position souhaitée
                $pdf->SetFont('helvetica', '', 10);
                // Contenu du paragraphe "Contact"
                $contentContact = '
                    <p><b> Contact :</b><br><br>
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

                $posX = 138;
                $posY = 60;

                if (file_exists($profileImagePath)) {
                    // Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
                    $pdf->Image($profileImagePath, $posX, $posY, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);
                } else {
                    // Utilisez une image anonyme
                    $pdf->Image('img/anonyme.jpg', 130, $posY, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);
                }

                foreach ($tests as $test) {
                    // Ajout d'une nouvelle page pour chaque test
                    $pdf->AddPage();
                    $pdf->setJPEGQuality(75);

                    // Calcul des dimensions de la page
                    $largeurPage = $pdf->getPageWidth() + 30;
                    $hauteurPage = $pdf->getPageHeight() - 25;

                    $pdf->SetX(10); // Définir la position X pour les informations du joueur
                    // Configuration de la police et des couleurs
                    $pdf->SetFont('helvetica', '', 20);
                    $pdf->SetTextColor(0, 0, 0);

                    $pdf->MultiCell(70, 10, $user->getFirstName() . ' ' . $user->getLastName(), 0, 'C', 0, 1, '10', '45', true);
                    $pdf->SetFont('helvetica', '', 15);

                    $pdf->MultiCell(70, 0, $user->getDateNaissance()->format('d/m/Y') . ' ' . '(' . $user->getCategory() . ')', 0, 'C', 0, 1, '10', '55', true);
                    $pdf->SetFontSize(10); // Rétablir la taille de police à la valeur par défaut (si nécessaire)

                    $pdf->MultiCell(70, 0, 'Date du test : ' . $test->getDate()->format('d/m/Y'), 0, 'C', 0, 1, '10', '65', true);
                    $pdf->SetFontSize(10); // Rétablir la taille de police à la valeur par défaut (si nécessaire)

                    $profileImagePath = 'uploads/images/' . $user->getId() . '.jpg';

                    $posX = 138;
                    $posY = 60;

                    if (file_exists($profileImagePath)) {
                        // Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
                        $pdf->Image($profileImagePath, $posX, $posY, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);
                    } else {
                        // Utilisez une image anonyme
                        $pdf->Image('img/anonyme.jpg', 130, $posY, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);
                    }

                    // Contenu du joueur (avec HTML)
                    $contentInfos = '
                    <style>.link { color: rgb(42, 56, 114) }</style>
                    <br><br><br><br><br><br><br><br><br><br><br><br>
                    <b><i>Informations du joueur : </i></b>
                    <br><hr><br><div></div><div></div><div></div>
                    <u>Email de contact</u> : ' . $user->getEmail() . '
                    </p>';

                    // Ajout du contenu du joueur au PDF
                    $pdf->SetFont('helvetica', '', 10);
                    $pdf->writeHTMLCell(70, 230, '', '', $contentInfos, 0, 0, 0, true, '', true);

                    // Définir les largeurs des colonnes pour le tableau des tests
                    $vmaColumnWidth = 50;
                    $cooperColumnWidth = 80;
                    $demiCooperColumnWidth = 80;
                    $jongleGaucheColumnWidth = 50;
                    $jongleDroitColumnWidth = 50;
                    $jongleTeteColumnWidth = 50;
                    $conduiteBalleColumnWidth = 90;
                    $vitesseColumnWidth = 70;

                    // Créer le tableau HTML pour les tests (partie 1)
                    $htmlTableTestsPart1 = '
                    <table border="0.5" cellpadding="4">
                        <thead>
                            <tr align="center">
                                <th width="' . $vmaColumnWidth . '"><b>VMA (km/h)</b></th>
                                <th width="' . $cooperColumnWidth . '"><b>Cooper (mètres)</b></th>
                                <th width="' . $demiCooperColumnWidth . '"><b>Demi-cooper (mètres)</b></th>
                                <th width="' . $jongleGaucheColumnWidth . '"><b>Jongles pied gauche</b></th>
                            </tr>
                        </thead>
                        <tbody>';
                    // Ajouter les données des tests dans le tableau (partie 1)
                    $htmlTableTestsPart1 .= '
                            <tr align="center">
                                <td width="' . $vmaColumnWidth . '">' . $test->getVma() . '</td>
                                <td width="' . $cooperColumnWidth . '">' . $test->getCooper() . '</td>
                                <td width="' . $demiCooperColumnWidth . '">' . $test->getDemiCooper() . '</td>
                                <td width="' . $jongleGaucheColumnWidth . '">' . $test->getJongleGauche() . '</td>
                            </tr>';
                    $htmlTableTestsPart1 .= '
                        </tbody>
                    </table>';

                    // Créer le tableau HTML pour les tests (partie 2)
                    $htmlTableTestsPart2 = '
                    <table border="0.5" cellpadding="4">
                        <thead>
                            <tr align="center">
                                <th width="' . $jongleDroitColumnWidth . '"><b>Jongles pied droit</b></th>
                                <th width="' . $jongleTeteColumnWidth . '"><b>Jongles tête</b></th>
                                <th width="' . $conduiteBalleColumnWidth . '"><b>Conduite de balle (secondes)</b></th>
                                <th width="' . $vitesseColumnWidth . '"><b>Vitesse (secondes)</b></th>
                            </tr>
                        </thead>
                        <tbody>';
                    // Ajouter les données des tests dans le tableau (partie 2)
                    $htmlTableTestsPart2 .= '
                            <tr align="center">
                                <td width="' . $jongleDroitColumnWidth . '">' . $test->getJongleDroit() . '</td>
                                <td width="' . $jongleTeteColumnWidth . '">' . $test->getJongleTete() . '</td>
                                <td width="' . $conduiteBalleColumnWidth . '">' . $test->getConduiteBalle() . '</td>
                                <td width="' . $vitesseColumnWidth . '">' . $test->getVitesse() . '</td>
                            </tr>';
                    $htmlTableTestsPart2 .= '
                        </tbody>
                    </table>';

                    // Calculer la hauteur estimée des deux tableaux
                    $htmlTableTestsPart1Height = count($tests) * 20;
                    $htmlTableTestsPart2Height = count($tests) * 20;

                    // Calculer la position Y du premier tableau pour le centrer verticalement
                    $htmlTableTestsPart1Y = ($pdf->getPageHeight() - $htmlTableTestsPart1Height - $htmlTableTestsPart2Height) / 2;

                    // Position Y du premier tableau
                    $htmlTableTestsPart1Y = 130;

                    // Position Y du deuxième tableau
                    $htmlTableTestsPart2Y = $htmlTableTestsPart1Y + $htmlTableTestsPart1Height + 25;

                    // Déplacer le curseur pour afficher le premier tableau
                    $pdf->SetY($htmlTableTestsPart1Y);
                    $pdf->SetX(110); // Assurez-vous que cela correspond à la position X que vous souhaitez

                    // Afficher le premier tableau HTML dans le PDF
                    $pdf->writeHTML($htmlTableTestsPart1, true, false, false, false, '');

                    // Déplacer le curseur pour afficher le deuxième tableau
                    $pdf->SetY($htmlTableTestsPart2Y); // Définir la position Y du deuxième tableau sous le premier
                    $pdf->SetX(110); // Assurez-vous que cela correspond à la position X que vous souhaitez

                    // Afficher le deuxième tableau HTML dans le PDF
                    $pdf->writeHTML($htmlTableTestsPart2, true, false, false, false, '');

                    // Déplacer le curseur vers le bas de la page
                    $pdf->SetY($hauteurPage - 80); // Ajustez la valeur en fonction de la position souhaitée
                    $pdf->SetFont('helvetica', '', 10);
                    // Contenu du paragraphe "Contact"
                    $contentContact = '
                        <p><b> Contact :</b><br><br>
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

                    $posX = 138;
                    $posY = 60;

                    if (file_exists($profileImagePath)) {
                        // Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
                        $pdf->Image($profileImagePath, $posX, $posY, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);
                    } else {
                        // Utilisez une image anonyme
                        $pdf->Image('img/anonyme.jpg', 130, $posY, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);
                    }
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