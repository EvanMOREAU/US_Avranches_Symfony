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

                    $pdf->SetFont('helvetica', '', 20);
                
                    $pdf->SetTextColor(0,0,0);
                    $pdf->MultiCell(80, 10, "FICHE DU JOUEUR", 0, '', 0, 1, '', '', false, 0, false, false, 0, '');
                    $pdf->MultiCell(70, 10, $user->getFirstName().' '.$user->getLastName().' ('.$user->getId(). ')', 0, 'C', 0, 1, '', '', true);
                    
                    $pdf->SetFont('helvetica', 'B', 20);
                    $pdf->SetTextColor(255,255,255);

                    $pdf->SetTextColor(255,255,255);

                    $textg = '
                    <style>.link { color: rgb(42,56,114); }</style>
                    <br><br><br><br>
                    <b><i>Informations concernant le joueur : </i></b>
                    <br><br>
                    <p><b>Date d\'anniversaire : </b>'. $user->getDateNaissance()->format('d/m/Y') .'
                    <br><hr><br><div></div>
                    <b>Catégorie</b> : '. $user->getCategory().'
                    <br><hr><br><div></div>
                    <b>Contact :</b>
                    </p><p>
                    <br> Christelle DELARUE<br>
                    <br>
                    Club House US Avranches MSM<br>
                    Allée Jacques Anquetil<br>
                    50300 Avranches.<br><br>
                    Tel : 02.33.48.30.78 <br><br>
                    Mail : <span class="link"><u>communication@us-avranches.fr</u></span><br>
                    Mail : <span class="link"><u>partenaires@us-avranches.fr</u></span><br>
                    Mail : <span class="link"><u>us.avranches@orange.fr</u></span>
                    </p>';


                    $pdf->SetFont( 'helvetica', '', 10 );
                    $pdf->writeHTMLCell( 65, 230, '', '', $textg, 0, 0, 0, true, '', true );

                    $pdf->Image('img/anonyme.jpg', 130, 33.3, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);

                    return $pdf->Output( 'US-Avranches-' . '.pdf', 'I' );
            }
        }
    }
}