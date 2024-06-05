<?php
namespace App\Controller; // Assurez-vous que le namespace correspond à l'emplacement de votre contrôleur

use App\Entity\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Repository\AttendanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ExcelController extends AbstractController
{

    #[Route('/excel', name: 'app_excel')]
    public function excel(EntityManagerInterface $entityManagerInterface, AttendanceRepository $attendanceRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page');
        }
        
        // Récupérez les données de la base de données
        $users = $entityManagerInterface->getRepository(User::class)->findByRole('ROLE_PLAYER');

        // Triez les utilisateurs par leur date de naissance
        usort($users, function ($a, $b) {
            return $a->getDateNaissance() <=> $b->getDateNaissance();
        });

        // Créez un objet Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Définissez le nom de la feuille Excel
        $spreadsheet->getActiveSheet()->setTitle('Informations des joueurs');

        // Créez une feuille de calcul
        $sheet = $spreadsheet->getActiveSheet();

        // Ajoutez les en-têtes de colonnes
        $sheet->setCellValue('A1', 'Nom');
        $sheet->setCellValue('B1', 'Prénom');
        $sheet->setCellValue('C1', 'Date de Naissance');
        $sheet->setCellValue('D1', 'Catégorie');
        $sheet->setCellValue('E1', 'Taux de présence entraînement');
        $sheet->setCellValue('F1', 'Pourcentage présence entraînement');
        $sheet->setCellValue('G1', 'Taux de présence match');
        $sheet->setCellValue('H1', 'Pourcentage présence match');

        // Mettez en gras les en-têtes
        $headerStyle = $sheet->getStyle('A1:D1');
        $headerFont = $headerStyle->getFont();
        $headerFont->setBold(true);

        // Ajoutez d'autres en-têtes et définissez la largeur des colonnes comme nécessaire
        $sheet->getColumnDimension('A')->setWidth(20); // Par exemple, largeur de la colonne A
        $sheet->getColumnDimension('B')->setWidth(20); // Par exemple, largeur de la colonne B
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(20);

        // Ajoutez les données à la feuille de calcul
        $row = 2; // Commencez à partir de la ligne 2
        $numTest = 0; // Initialisez le numéro de test en dehors de la boucle des utilisateurs
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->getLastName());
            $sheet->setCellValue('B' . $row, $user->getFirstName());
            $sheet->setCellValue('C' . $row, $user->getDateNaissance()->format('d/m/Y'));
            $sheet->setCellValue('D' . $row, $user->getCategory());

            $att = $attendanceRepository->findByUserId($user->getId());
            $countEntMake = 0;
            $countEnt = 0;
            $countMatchMake = 0;
            $countMatch = 0;
            foreach( $att as $rowatt ){
                
                if($rowatt->getGathering()->getType() == "Entraînement"){
                    $countEnt++;
                    if($rowatt->isIsPresent() == true){
                        $countEntMake++;
                    }
                }elseif($rowatt->getGathering()->getType() == "Match"){
                    $countMatch++;
                    if($rowatt->isIsPresent() == true){
                        $countMatchMake++;
                    }
                }

            }

            if($countEntMake>0){
                $pourcentEnt = ($countEntMake/$countEnt) * 100;
                $sheet->setCellValue('F' . $row, $pourcentEnt.'%');
            }else{
                $sheet->setCellValue('F' . $row, 'Aucun match');
            }
 
            if($countMatchMake>0){
                $pourcentMatch = ($countMatchMake/$countMatch) * 100;
                $sheet->setCellValue('H' . $row, $pourcentMatch.'%');
            }else{
                $sheet->setCellValue('H' . $row, 'Aucun match');
            }
            
            $sheet->setCellValue('E' . $row, $countEntMake.'/'.$countEnt);
            $sheet->setCellValue('G' . $row, $countMatchMake.'/'.$countMatch);
            //------------------- TESTS -------------------

            // Ajoutez une feuille uniquement si l'utilisateur a des tests
            if ($user->getTests()->count() > 0) {

                // Réinitialisez le numéro du test à l'intérieur de la boucle des utilisateurs
                $numTest = 0;

                // Créez une nouvelle feuille pour chaque test
                $testSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $user->getLastName() . ' ' . $user->getFirstName());
                $spreadsheet->addSheet($testSheet);
                $spreadsheet->setActiveSheetIndexByName($user->getLastName() . ' ' . $user->getFirstName());
                $testRow = 1;

                // Ajoutez les en-têtes de colonnes pour les tests
                $testSheet->setCellValue('A1', 'Numéro du test');
                $testSheet->setCellValue('B1', 'VMA (km/h)');
                $testSheet->setCellValue('C1', 'Cooper (12 min en mètres)');
                $testSheet->setCellValue('D1', 'Demi-Cooper (6 min en mètres)');
                $testSheet->setCellValue('E1', 'Jongles pied gauche');
                $testSheet->setCellValue('F1', 'Jongles pied droit');
                $testSheet->setCellValue('G1', 'Jongles tête');
                $testSheet->setCellValue('H1', 'Date test');
                $testSheet->setCellValue('I1', 'Conduite de balle (secondes)');
                $testSheet->setCellValue('J1', 'Vitesse (secondes)');
                
                $testRow++;

                // Mettez en gras les en-têtes
                $headerStyle = $testSheet->getStyle('A1:P1');
                $headerFont = $headerStyle->getFont();
                $headerFont->setBold(true);

                // Ajoutez d'autres en-têtes et définissez la largeur des colonnes comme nécessaire
                $testSheet->getColumnDimension('A')->setWidth(30); // Par exemple, largeur de la colonne A
                $testSheet->getColumnDimension('B')->setWidth(30); // Par exemple, largeur de la colonne B
                $testSheet->getColumnDimension('C')->setWidth(30);
                $testSheet->getColumnDimension('D')->setWidth(30);
                $testSheet->getColumnDimension('E')->setWidth(30);
                $testSheet->getColumnDimension('F')->setWidth(30);
                $testSheet->getColumnDimension('G')->setWidth(30);
                $testSheet->getColumnDimension('H')->setWidth(30);
                $testSheet->getColumnDimension('I')->setWidth(30);
                $testSheet->getColumnDimension('J')->setWidth(30);
                $testSheet->getColumnDimension('K')->setWidth(10);
                $testSheet->getColumnDimension('L')->setWidth(30);
                $testSheet->getColumnDimension('M')->setWidth(30);
                $testSheet->getColumnDimension('N')->setWidth(10);
                $testSheet->getColumnDimension('O')->setWidth(30);
                $testSheet->getColumnDimension('P')->setWidth(30);

                // Ajoutez les données à la feuille de calcul pour les tests
                $testRow = 2; // Commencez à partir de la ligne .
                foreach ($user->getTests() as $test) {

                    $numTest++; // Incrémentez le numéro du test à chaque itération
                    $testSheet->setCellValue('A' . $testRow, 'n°' . $numTest);
                    $testSheet->setCellValue('B' . $testRow, $test->getVma() . '');
                    $testSheet->setCellValue('C' . $testRow, $test->getCooper() . '');
                    $testSheet->setCellValue('D' . $testRow, $test->getDemiCooper() . '');
                    $testSheet->setCellValue('E' . $testRow, $test->getJongleGauche());
                    $testSheet->setCellValue('F' . $testRow, $test->getJongleDroit());
                    $testSheet->setCellValue('G' . $testRow, $test->getJongleTete());
                    $testSheet->setCellValue('H' . $testRow, $test->getDate());
                    $testSheet->setCellValue('I' . $testRow, $test->getConduiteBalle() . '');
                    $testSheet->setCellValue('J' . $testRow, $test->getVitesse() . '');

                    // Continuez à ajouter les données nécessaires dans les colonnes suivantes
                    $testRow++;
                }
            }

            // Sélectionnez la première feuille pour la prochaine itération
            $spreadsheet->setActiveSheetIndex(0);
            $row++;
        }

        // Créez une réponse pour le fichier Excel
        $response = new Response();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        ob_start();
        $writer->save('php://output');
        $excelUsers = ob_get_clean();

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.xlsx"');
        $response->setContent($excelUsers);

        return $response;

        // Créez un objet Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Définissez le nom de la feuille Excel
        $spreadsheet->getActiveSheet()->setTitle('Calendrier des rassemblements');

        // Ajoutez votre code pour ajouter les événements au fichier Excel ici

        // Créez une réponse pour le fichier Excel
        $response = new Response();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        ob_start();
        $writer->save('php://output');
        $excelContent = ob_get_clean();

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="calendrier_rassemblements.xlsx"');
        $response->setContent($excelContent);

        return $response;
    }
}