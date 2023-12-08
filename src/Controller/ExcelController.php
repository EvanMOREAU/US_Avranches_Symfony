<?php
namespace App\Controller; // Assurez-vous que le namespace correspond à l'emplacement de votre contrôleur

use App\Entity\User;
use App\Entity\Height;
use App\Entity\Weight;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExcelController extends AbstractController
{

    #[Route('/excel', name: 'app_excel')]
    public function excel(): Response
    {
        // Récupérez les données de la base de données
        $users = $this->getDoctrine()->getRepository(User::class)->findByRole('ROLE_PLAYER');

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

        // Mettez en gras les en-têtes
        $headerStyle = $sheet->getStyle('A1:D1');
        $headerFont = $headerStyle->getFont();
        $headerFont->setBold(true);

        // Ajoutez d'autres en-têtes et définissez la largeur des colonnes comme nécessaire
        $sheet->getColumnDimension('A')->setWidth(20); // Par exemple, largeur de la colonne A
        $sheet->getColumnDimension('B')->setWidth(20); // Par exemple, largeur de la colonne B
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);

        // Ajoutez les données à la feuille de calcul
        $row = 2; // Commencez à partir de la ligne 2
        $numTest = 0; // Initialisez le numéro de test en dehors de la boucle des utilisateurs
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->getLastName());
            $sheet->setCellValue('B' . $row, $user->getFirstName());
            $sheet->setCellValue('C' . $row, $user->getDateNaissance()->format('d/m/Y'));
            $sheet->setCellValue('D' . $row, $user->getCategory());

            //------------------- TESTS -------------------

            // Ajoutez une feuille uniquement si l'utilisateur a des tests
            if ($user->getTests()->count() > 0) {

                // Réinitialisez le numéro du test à l'intérieur de la boucle des utilisateurs
                $numTest = 0;

                // Créez une nouvelle feuille pour chaque test
                $testSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $user->getLastName() . ' ' . $user->getFirstName() . ' - Tests');
                $spreadsheet->addSheet($testSheet);
                $spreadsheet->setActiveSheetIndexByName($user->getLastName() . ' ' . $user->getFirstName() . ' - Tests');

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
                $testSheet->setCellValue('K1', 'Poids (kilogrammes)');
                $testSheet->setCellValue('L1', 'Taille (centimètres)');

                // Mettez en gras les en-têtes
                $headerStyle = $testSheet->getStyle('A1:J1');
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
                $testSheet->getColumnDimension('K')->setWidth(30);
                $testSheet->getColumnDimension('L')->setWidth(30);

                // Ajoutez les données à la feuille de calcul pour les tests
                $testRow = 2; // Commencez à partir de la ligne .
                foreach ($user->getTests() as $test) {

                    $numTest++; // Incrémentez le numéro du test à chaque itération
                    $testSheet->setCellValue('A' . $testRow, 'n°' . $numTest);
                    $testSheet->setCellValue('B' . $testRow, $test->getVma() . ' km/h');
                    $testSheet->setCellValue('C' . $testRow, $test->getCooper() . ' mètres');
                    $testSheet->setCellValue('D' . $testRow, $test->getDemiCooper() . ' mètres');
                    $testSheet->setCellValue('E' . $testRow, $test->getJongleGauche());
                    $testSheet->setCellValue('F' . $testRow, $test->getJongleDroit());
                    $testSheet->setCellValue('G' . $testRow, $test->getJongleTete());
                    $testSheet->setCellValue('H' . $testRow, $test->getDate());
                    $testSheet->setCellValue('I' . $testRow, $test->getConduiteBalle() . ' secondes');
                    $testSheet->setCellValue('J' . $testRow, $test->getVitesse() . ' secondes');

                    // Continuez à ajouter les données nécessaires dans les colonnes suivantes
                    $testRow++;
                }
            }

            //------------------- POIDS -------------------

            // Réinitialisez le numéro du test à l'intérieur de la boucle des utilisateurs
            $numWeight = 0;

            // Créez une nouvelle feuille pour chaque test
            $weightSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $user->getLastName() . ' ' . $user->getFirstName() . ' - POIDS');
            $spreadsheet->addSheet($weightSheet);
            $spreadsheet->setActiveSheetIndexByName($user->getLastName() . ' ' . $user->getFirstName() . ' - POIDS');

            // Ajoutez les en-têtes de colonnes pour les tests
            $weightSheet->setCellValue('A1', 'Numéro du test');
            $weightSheet->setCellValue('B1', 'Date');
            $weightSheet->setCellValue('C1', 'Poids (kilogrammes)');

            // Mettez en gras les en-têtes
            $headerStyle = $weightSheet->getStyle('A1:C1');
            $headerFont = $headerStyle->getFont();
            $headerFont->setBold(true);

            // Ajoutez d'autres en-têtes et définissez la largeur des colonnes comme nécessaire
            $weightSheet->getColumnDimension('A')->setWidth(30); // Par exemple, largeur de la colonne A
            $weightSheet->getColumnDimension('B')->setWidth(30); // Par exemple, largeur de la colonne B
            $weightSheet->getColumnDimension('C')->setWidth(30);


            $weightRow = 2; // Commencez à partir de la ligne .
            foreach ($user->getWeights() as $weight) {

                // Récupérez les informations de poids
                $weight = $this->getDoctrine()->getRepository(Weight::class)->findOneBy(['user' => $user]);

                $numWeight++; // Incrémentez le numéro du poids à chaque itération
                $weightSheet->setCellValue('A' . $weightRow, 'n°' . $numWeight);
                $weightSheet->setCellValue('B' . $weightRow, $weight->getDate()->format('d/m/Y'));

                // Vérifiez si l'objet Weight n'est pas null avant d'appeler getValue()
                $weightSheet->setCellValue('C' . $weightRow, $weight ? $weight->getValue() . ' kg' : '');

                // Continuez à ajouter les données nécessaires dans les colonnes suivantes
                $weightRow++;
            }


            //------------------- TAILLE -------------------

            // Réinitialisez le numéro du test à l'intérieur de la boucle des utilisateurs
            $numHeight = 0;

            // Créez une nouvelle feuille pour chaque test
            $heightSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $user->getLastName() . ' ' . $user->getFirstName() . ' - TAILLE');
            $spreadsheet->addSheet($heightSheet);
            $spreadsheet->setActiveSheetIndexByName($user->getLastName() . ' ' . $user->getFirstName() . ' - TAILLE');

            // Ajoutez les en-têtes de colonnes pour les tests
            $heightSheet->setCellValue('A1', 'Numéro du test');
            $heightSheet->setCellValue('B1', 'Date');
            $heightSheet->setCellValue('C1', 'Taille (centimètres)');

            // Mettez en gras les en-têtes
            $headerStyle = $heightSheet->getStyle('A1:C1');
            $headerFont = $headerStyle->getFont();
            $headerFont->setBold(true);

            // Ajoutez d'autres en-têtes et définissez la largeur des colonnes comme nécessaire
            $heightSheet->getColumnDimension('A')->setWidth(30); // Par exemple, largeur de la colonne A
            $heightSheet->getColumnDimension('B')->setWidth(30); // Par exemple, largeur de la colonne B
            $heightSheet->getColumnDimension('C')->setWidth(30);

            $heightRow = 2; // Commencez à partir de la ligne .

            // Déclarez la variable $height avant la boucle
            $height = null;

            // Vérifiez si l'utilisateur a des hauteurs avant d'entrer dans la boucle
            if ($user->getHeights()->count() > 0) {
                foreach ($user->getHeights() as $height) {
                    // Récupérez les informations de poids
                    $height = $this->getDoctrine()->getRepository(Height::class)->findOneBy(['user' => $user]);

                    $numHeight++; // Incrémentez le numéro du poids à chaque itération
                    $heightSheet->setCellValue('A' . $heightRow, 'n°' . $numHeight);
                    $heightSheet->setCellValue('B' . $heightRow, $height->getDate()->format('d/m/Y'));

                    // Vérifiez si l'objet Height n'est pas null avant d'appeler getValue()
                    $heightSheet->setCellValue('C' . $heightRow, $height ? $height->getValue() . ' cm' : '');

                    // Continuez à ajouter les données nécessaires dans les colonnes suivantes
                    $heightRow++;
                }
            }


            // Sélectionnez la première feuille pour la prochaine itération
            $spreadsheet->setActiveSheetIndex(0);
        }

        $row++;

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
    }
}