<?php

namespace App\Controller;

use TCPDF;
use App\Entity\Pdf;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfController extends AbstractController
 {
    #[ Route( '/pdf', name: 'app_pdf' ) ]

    public function pdf( PlayerRepository $playerRepository ): Response
 {
        $pdf = new Pdf();

        $players = $playerRepository->find( 1 );

        $pdf->SetAuthor( 'SIO TEAM ! 💻' );
        $pdf->SetTitle( 'Fiche joueur' );
        $pdf->SetFont( 'times', '', 14 );

        $pdf->AddPage();
        $pdf->setJPEGQuality( 75 );

        $largeurPage = $pdf->getPageWidth() + 25;
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

        $pdf->SetFont('helvetica', 'B', 25);

        // $pdf->Image('img/logo_usa.jpg', '', '', 20, 20, '', '', '', false, 100, '', false, false, 0, false, false, false);
       //$pdf->Image('img/usavranches.jpg', 15, 140, 75, 113, 'JPG', 'http://localhost:8000/pdf', '', true, 150, '', false, false, 1, false, false, false);
       
        $pdf->SetTextColor(255,255,255);
        $pdf->MultiCell(80, 10, "FICHE DU JOUEUR : ", 0, '', 0, 1, '', '', false, 0, false, false, 0, '');
        $pdf->MultiCell(80, 10, $players->getFirstName().' '.$players->getLastName(), 0, 'C', 0, 1, '', '', true);
        
        $pdf->SetFont('helvetica', 'B', 17);
        $pdf->SetTextColor(255,255,255);

        $pdf->SetTextColor(255,255,255);

        $textg = '
        <style>hr {
            color: rgb( 0, 63, 144 );
        }
        </style>
        <p><b>Objectif de la formation</b>
        <hr>'. $players->getBirthdate()->format('d/m/Y') .'
        <b>Prérequis necessaire / public visé</b>
        <b>Modalités d\'accès et d\'inscription</b>
        <hr><br><div></div>
        <u>Dates</u> : '. $players->getMatchesPlayed();

        $pdf->SetFont( 'helvetica', '', 10 );
        $pdf->writeHTMLCell( 65, 230, '', '', $textg, 0, 0, 0, true, '', true );

        $pdf->SetFont( 'helvetica', '', 10 );
        $pdf->writeHTMLCell( 120, 230, '', '', $textg, 0, 0, 0, true, '', true );

        return $pdf->Output( 'US-Avranches-' . '.pdf', 'I' );

    }
}