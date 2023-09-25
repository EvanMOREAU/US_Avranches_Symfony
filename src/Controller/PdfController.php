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
        $pdf->SetTitle('Coucou c un pdf magique vide');
        $pdf->SetFont('times', '', 14);
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->setCellMargins(1, 1, 1, 1);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->SetFillColor(108,212,255);
        $pdf->SetTextColor(0,0,0);
        $pdf->Image('images/fcpro.jpg', 8, 10, 39, 35, 'JPG', 'https://fcpro-rtirbois.bts.sio-ndlp.fr/page/1', '', true, 150, '', false, false, 0, false, false, false);
        $pdf->MultiCell(187, 20, "PROGRAMME DE FORMATION", 0, 'C', 1, 1, '', '', true, 0, false, true, 20, 'M');

        $pdf->SetFont('helvetica', 'B', 17);
        $pdf->SetFillColor(108,212,255);
        $pdf->SetTextColor(255,255,255);
        $pdf->MultiCell(187, 10, '$formation->getName()', 0, 'C', 1, 1, '', '', true);
        
        $pdf->setCellPaddings(3,3,3,3);
        $textg = '
        <style> .black { color: rgb(0,0,0); } .link { color: rgb(100,0,0); }</style>
        <br>
        <p class="black">
<b>Tarifs :</b></p>
'.' 40 € net.
        <br>
        <p class="black">
<b>Modalités :</b>
        </p>
'. 'Coucou '. '
        <div></div>
        <p class="black">
<b>Accessibilité aux personnes handicapées :</b>
</p><p>
<b>Accès au lieu de formation</b> :<br>
Les locaux sont accessibles aux
personnes en situation de handicap,
merci de nous contacter.<br>
<br>
<b>Accès à la prestation</b> :<br>
Une adaptation de la formation est
possible pour les personnes en
situation de handicap, merci de nous
contacter.
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
        $pdf->SetFillColor(108,212,255);
        $pdf->writeHTMLCell(65, 230, "", "", $textg, 0, 0, 1, true, '', true);

        $textd = '
        <style>hr { color: rgb(0, 63,144); }</style>
        <p><b>Objectif de la formation</b>
        <hr>'.' $formation->getObjectif() '.'
        <b>Prérequis necessaire / public visé</b>
        <hr>'. '$formation->getPrerequis()' .'
        <b>Modalités d\'accès et d\'inscription</b>
        <hr><br><div></div>
<u>Dates</u> : '.' $formation->getStartDateTime()->format(d/m/Y) '.' à '. '$formation->getEndDateTime()->format(d/m/Y)' .'<br>
<u>Lieu</u> : ' . '$formation->getPlace()' . '
<br><br>
Nombre de stagiaires minimal : ' . '$formation->getCapacityMin()' . ' – Nombre de stagiaires maximal : '. '$formation->getCapacity()' .'<br>
<i>Si le minimum requis de participants n’est pas atteint la session de formation
ne pourra avoir lieu.</i>
<br>

'. '$formation->getModalites()' .'<br>
<b>Moyens pédagogiques et techniques</b>
        <hr>'. '$formation->getMoyenPedagogique()' .'<br>
<b>Modalité d\'évaluation</b>
        <hr>'.' $formation->getEvaluation()' .'
        ';

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->writeHTMLCell(120, 230, "", "", $textd, 0, 0, 1, true, '', true);

        return $pdf->Output('US-Avranches-' . '.pdf','I');
    
    }}