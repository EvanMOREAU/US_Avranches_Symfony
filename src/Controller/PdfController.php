<?php

namespace App\Controller;

use TCPDF;
use App\Entity\Pdf;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PdfController extends AbstractController
 {
    #[ Route( '/pdf', name: 'app_pdf' ) ]

    public function pdf( UserRepository $userRepository ): Response
    {
        $token = $this->get('security.token_storage')->getToken();

        $pdf = new Pdf();
        if ($token instanceof TokenInterface) {
            $user = $token->getUser();

            if ($user instanceof User) {


                $pdf->SetAuthor( 'SIO TEAM ! 💻' );
                $pdf->SetTitle( 'Fiche joueur' );
                $pdf->SetFont( 'times', '', 14 );

                $pdf->AddPage();
                $pdf->setJPEGQuality( 75 );

                $largeurPage = $pdf->getPageWidth() + 30;
                $hauteurPage = $pdf->getPageHeight()- 25;

                // $pdf->Image(
                //     $backgroundImage = 'img/US-Avranches.jpg',  // Chemin vers l'image
                // 0,                 // Position X de l'image
                //     0,                 // Position Y de l'image
                // $largeurPage,      // Largeur de l'image ( largeur de la page )
                //     $hauteurPage,      // Hauteur de l'image (hauteur de la page)
                // '',                // Lien associé à l'image ( vide dans cet exemple )
                //     '',                // Lien alternatif ( vide dans cet exemple )
                //     '',                // Texte alternatif ( vide dans cet exemple )
                //     false,             // Image est un lien ( false dans cet exemple )
                //     300,               // Qualité de l'image
                // '',                // Format de l'image ( vide dans cet exemple )
                //     false,             // Compression ( false dans cet exemple )
                //     false,             // Masque ( false dans cet exemple )
                //     0                  // Positionnement de l'image
                // ); 

                $pdf->SetFont('helvetica', '', 20);

                // $pdf->Image('img/logo_usa.jpg', '', '', 20, 20, '', '', '', false, 100, '', false, false, 0, false, false, false);
                //$pdf->Image('img/usavranches.jpg', 15, 140, 75, 113, 'JPG', 'http://localhost:8000/pdf', '', true, 150, '', false, false, 1, false, false, false);
            
                $pdf->SetTextColor(0,0,0);
                $pdf->MultiCell(80, 10, "FICHE DU JOUEUR", 0, '', 0, 1, '', '', false, 0, false, false, 0, '');
                $pdf->MultiCell(70, 10, $user->getFirstName().' '.$user->getLastName().' ('.$user->getId(). ')', 0, 'C', 0, 1, '', '', true);
                
                $pdf->SetFont('helvetica', 'B', 20);
                $pdf->SetTextColor(255,255,255);

                $pdf->SetTextColor(255,255,255);

                $textg = '
                <br><br><br><br>
                <b><i><big>Informations concernant le joueur : </big></i></b>
                <br><br>
                <p><b>Date d\'anniversaire : </b>'. $user->getDateNaissance()->format('d/m/Y') .'
                <br><hr><br><div></div>
                <b>Catégorie</b> : '. $user->getCategory();

                $pdf->SetFont( 'helvetica', '', 10 );
                $pdf->writeHTMLCell( 65, 230, '', '', $textg, 0, 0, 0, true, '', true );

                $pdf->Image('img/anonyme.jpg', 130, 33.3, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);

                return $pdf->Output( 'US-Avranches-' . '.pdf', 'I' );
            }
        }
    }
}