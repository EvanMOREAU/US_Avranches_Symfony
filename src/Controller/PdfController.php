<?php

namespace App\Controller;

use TCPDF;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfController extends AbstractController
{
    #[Route('/pdf', name: 'app_pdf')]
    public function pdf(PlayerRepository $playerRepository): Response
    {
        $pdf = new \TCPDF();

        $players = $playerRepository->find(1);

        $pdf->SetAuthor('SIO TEAM ! 💻');
        $pdf->SetTitle('Fiche joueur');
        $pdf->SetFont('times', '', 14);
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->setCellMargins(1, 1, 1, 1);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();
        $pdf->setJPEGQuality(75);
        
        
        $largeurPage = $pdf->getPageWidth() + 120;
        $hauteurPage = $pdf->getPageHeight()+ -100;

        $pdf->Image(
        $backgroundImage = 'img/US-Avranches.jpg',  // Chemin vers l'image
        0,                 // Position X de l'image
        0,                 // Position Y de l'image
        $largeurPage,      // Largeur de l'image (largeur de la page)
        $hauteurPage,      // Hauteur de l'image (hauteur de la page)
        '',                // Lien associé à l'image (vide dans cet exemple)
        '',                // Lien alternatif (vide dans cet exemple)
        '',                // Texte alternatif (vide dans cet exemple)
        false,             // Image est un lien (false dans cet exemple)
        300,               // Qualité de l'image
        '',                // Format de l'image (vide dans cet exemple)
        false,             // Compression (false dans cet exemple)
        false,             // Masque (false dans cet exemple)
        0                  // Positionnement de l'image
        );

        $pdf->SetFont('helvetica', 'B', 25);

        $pdf->SetXY(0, 1);
        $pdf->Image('img/logo_usa.jpg', '', '', 20, 20, '', '', '', false, 100, '', false, false, 0, false, false, false);
       
        $pdf->SetFillColor(31,40,97);
        $pdf->SetTextColor(255,255,255);
        $pdf->MultiCell(187, 20, "FICHE D'UN JOUEUR", 0, 'C', 1, 1, '', '', true, 0, false, true, 20, 'M');

        $pdf->SetFont('helvetica', 'B', 17);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetTextColor(255,255,255);
        $pdf->MultiCell(187, 10, $players->getFirstName(), 0, 'C', 1, 1, '', '', true);
        
        $pdf->SetTextColor(255,255,255);
        $pdf->setCellPaddings(1,1,1,1);
        $textg = '
        <style> .black { color: rgb(255,255,255); } .link { color: rgb(100,0,0); }</style>
        <br>
        <p class="black">
<b>A remplir :</b></p>
'.' A remplir
        <br>
        <p class="black">
<b>A remplir :</b>
        </p>
'. 'A remplir '. '
        <div></div>
        <p class="black">
<b>A remplir:</b>
</p><p>
<b>A remplir</b> :<br>
A remplir.<br>
<br>
<b>A remplir</b> :<br>
A remplir
        </p><br>
        <p class="black">
<b>Contact :</b>
        </p><p>
<b>Contact club : Christelle DELARUE</b><br>
Service de Formation Professionnelle<br>
Continue de l’OGEC Notre Dame de la Providence<br>
<br>
Club House US Avranches MSM, Allée Jacques Anquetil, 50300 AVRANCHES.<br>
Tel 02 33 48 30 78<br>
mail :  <span class="link">communication@us-avranches.fr</span><br>
        <span class="link">partenaires@us-avranches.fr</span><br>
        <span class="link">us.avranches@orange.fr</span><br>
<br>    
OF certifié QUALIOPI pour les actions de formations<br>
<br>
Site Web : <span class="link">https://ndlpavranches.fr/fc-pro/</span>
        </p>';

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(3,29,68);
        $pdf->writeHTMLCell(65, 230, "", "", $textg, 0, 0, 1, true, '', true);

        $textd = '
        <style>hr { color: rgb(0, 63,144); }</style>
        <b>Prérequis necessaire / public visé</b>
        '. '$pdf->getContent()' .'
        <b>Modalités d\'accès et d\'inscription</b>
        <br><div></div>
<br><br>

<b>Moyens pédagogiques et techniques</b>
<b>Modalité d\'évaluation</b>
        '.' $pdf->getStats()' .'
        ';

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->writeHTMLCell(120, 230, "", "", $textd, 0, 0, 1, true, '', true);

        return $pdf->Output('US-Avranches-' . '.pdf','I');
    
    }}