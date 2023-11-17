<?php

namespace App\Controller;

use TCPDF;
use App\Entity\Pdf;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PdfController extends AbstractController
{
    #[Route('/pdf', name: 'app_pdf')]
    public function pdf(UserRepository $userRepository): Response
    {
        // Récupérez le token d'authentification de l'utilisateur actuellement connecté.
        $token = $this->get('security.token_storage')->getToken();

        // Créez une nouvelle instance de la classe PDF.
        $pdf = new Pdf();

        if ($token instanceof TokenInterface) {
            // Récupérez l'utilisateur à partir du token d'authentification.
            $user = $token->getUser();

            if ($user instanceof User) {
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
                $pdf->MultiCell(70, 10, $user->getFirstName() . ' ' . $user->getLastName() . ' (' . $user->getId() . ')', 0, 'C', 0, 1, '', '', true);

                // Configuration de la police et des couleurs pour le contenu du joueur
                $pdf->SetFont('helvetica', 'B', 20);
                $pdf->SetTextColor(0, 0, 0);

                // Contenu du joueur (avec HTML)
                $textg = '
                <style>.link { color: rgb(42, 56, 114) }</style>
                <br><br><br><br>
                <b><i>Informations concernant le joueur : </i></b>
                <br><br>
                <p><b>Date de naissance : </b>' . $user->getDateNaissance()->format('d/m/Y') . '
                <br><hr><br><div></div>
                <b>Catégorie : </b>' . $user->getCategory() . '
                <br><hr><br><div></div>
                <b>Nombre de matchs joués : 2</b>
                <br><hr><br><div></div>
                <b>Poids : 61kg</b>
                <br><hr><br><div></div>
                <b>Taille : 173cm</b>
                <br><hr><br><div></div>
                </p>

                <p><b> Contact :</b>
                <br> ChristelleGamer DELARUE<br>
                <br>
                Club House US Avranches MSM<br>
                Allée Jacques Anquetil<br>
                50300 Avranches.<br><br>
                <b>Téléphone</b> : 02.33.48.30.78 <br><br>
                <b>Mails</b> :<br>
                <span class="link"><u>communication@us-avranches.fr</u></span><br>
                <span class="link"><u>partenaires@us-avranches.fr</u></span><br>
                <span class="link"><u>us.avranches@orange.fr</u></span>
                </p>';

                // Ajout du contenu du joueur au PDF
                $pdf->SetFont('helvetica', '', 10);
                $pdf->writeHTMLCell(65, 230, '', '', $textg, 0, 0, 0, true, '', true);

                // Ajout d'une image au PDF
                $pdf->Image('img/graph_'. $user->getFirstName() .'.jpg', 95, 150, 100, 100, '', '', '', false, 300, '', false, false, 1, false, false, false);

                // Ajout d'une image au PDF
                $pdf->Image('img/joueur.jpg', 130, 33.3, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);

                // Génération du PDF et envoi en réponse
                return $pdf->Output('US-Avranches-' . '.pdf', 'I');
            }
        }

        // Gestion des cas d'erreur
        return new Response('Erreur');
    }

    #[Route('/pdftest', name: 'app_pdftest')]
    public function pdfTest(): Response
    {
        $user = $this->getUser(); // Récupérez l'utilisateur actuellement connecté

        return $this->render('/pdf/index.html.twig', [
            'controller_name' => 'DefaultController',
            'user' => $user, // Assurez-vous que 'user' est correctement défini
        ]);
    }
}
