<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
    #[Route('/pdf', name: 'app_pdf')]
    public function index(): Response
    {
        $pdf = new \TCPDF();

        $pdf->SetAuthor('SIO TEAM ! 💻');
        $pdf->SetTitle('Fiche joueur');
        $pdf->SetFont('times', '', 14);
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->setCellMargins(1, 1, 1, 1);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();
        $pdf->setJPEGQuality(75);
        
        $pdf->SetFont('helvetica', 'B', 25);

        $pdf->SetXY(0, 1);
        $pdf->Image('img/logo_usa.jpg', '', '', 20, 20, '', '', '', false, 100, '', false, false, 0, false, false, false);
       // $pdf->Image('img/usavranches.jpg', 15, 140, 75, 113, 'JPG', 'http://localhost:8000/pdf', '', true, 150, '', false, false, 1, false, false, false);
       
        $pdf->SetFillColor(31,40,97);
        $pdf->SetTextColor(255,255,255);
        $pdf->MultiCell(187, 20, "FICHE D'UN JOUEUR", 0, 'C', 1, 1, '', '', true, 0, false, true, 20, 'M');

        $pdf->SetFont('helvetica', 'B', 17);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(187, 10, 'Arthur DELACOUR', 0, 'C', 1, 1, '', '', true);
        
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
        <hr>'. '$pdf->getContent()' .'
        <b>Modalités d\'accès et d\'inscription</b>
        <hr><br><div></div>
<br><br>

<b>Moyens pédagogiques et techniques</b>
<b>Modalité d\'évaluation</b>
        <hr>'.' $pdf->getStats()' .'
        ';

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->writeHTMLCell(120, 230, "", "", $textd, 0, 0, 1, true, '', true);

        return $pdf->Output('US-Avranches-' . '.pdf','I');
    
    }}