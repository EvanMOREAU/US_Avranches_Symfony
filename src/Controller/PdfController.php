<?php

namespace App\Controller;

use TCPDF;
use App\Entity\Pdf;
use App\Entity\User;
use App\Entity\Tests;
use App\Entity\Weight;
use App\Repository\UserRepository;
use App\Repository\TestsRepository;
use App\Service\UserVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PdfController extends AbstractController
{
    private $userVerificationService;

    public function __construct(UserVerificationService $userVerificationService)
    {
        $this->userVerificationService = $userVerificationService;
    }

    #[Route('/pdf', name: 'app_pdf')]

    public function pdf(UserRepository $userRepository, TestsRepository $testsRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->userVerificationService->verifyUser()) {
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        // Récupérez le token d'authentification de l'utilisateur actuellement connecté.
        $token = $this->get('security.token_storage')->getToken();

        // Créez une nouvelle instance de la classe PDF.
        $pdf = new Pdf();

        if ($token instanceof TokenInterface) {
            // Récupérez l'utilisateur à partir du token d'authentification.
            $user = $token->getUser();

            if ($user instanceof User) {

                $tests = $testsRepository->findBy(['user' => $user]);

                // Récupérez les tests triés par date décroissante
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

                // Ajout du titre de la fiche du joueur
                $pdf->MultiCell(80, 10, "FICHE DU JOUEUR", 0, '', 0, 1, '', '', false, 0, false, false, 0, '');

                // Ajout du nom du joueur
                $pdf->MultiCell(70, 10, $user->getFirstName() . ' ' . $user->getLastName(), 0, 'C', 0, 1, '', '', true);

                // Configuration de la police et des couleurs pour le contenu du joueur
                $pdf->SetFont('helvetica', 'B', 20);
                $pdf->SetTextColor(0, 0, 0);

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
                    <b>Nombre de matchs joués :</b> 2
                    <br><hr><br><div></div>
                    </p>
                    <p><b> Contact :</b>
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

                // Ajout du contenu du joueur au PDF
                $pdf->SetFont('helvetica', '', 10);
                $pdf->writeHTMLCell(65, 230, '', '', $contentInfos, 0, 0, 0, true, '', true);

                // Ajout d'une image au PDF
                $pdf->Image('img/anonyme.jpg', 130, 33.3, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);

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

                    // A MODIFIER LE POIDS ET LA TAILLE CAR NE FONCTIONNE PAS COMME CA DEVRAIT
                    $contentTests = '
                    <br><br><br>
                    <b>Poids : </b>' . $this->getWeightForDate($user, $test->getDate(), $entityManager) . ' kg
                    <br><hr><br><div></div>
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
                return $pdf->Output('US-Avranches-' . '.pdf', 'I');
            }
        }
        // Gestion des cas d'erreur
        return new Response('Erreur');
    }

    // A MODIFIER LE POIDS ET LA TAILLE CAR NE FONCTIONNE PAS COMME CA DEVRAIT
    private function getWeightForDate(User $user, \DateTimeInterface $testDate, EntityManagerInterface $entityManager): ?float
    {
        // Utilisez le repository de l'entité Weight
        $weightEntry = $entityManager->getRepository(Weight::class)->findOneBy(['user' => $user, 'date' => $testDate]);
    
        return $weightEntry ? $weightEntry->getValue() : $user->getWeight();
    }
}