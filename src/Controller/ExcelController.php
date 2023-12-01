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
        $headerStyle = $sheet->getStyle('A1:F1');
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

            // Ajoutez une feuille uniquement si l'utilisateur a des tests
            if ($user->getTests()->count() > 0) {

                // Réinitialisez le numéro du test à l'intérieur de la boucle des utilisateurs
                $numTest = 0;

                // Créez une nouvelle feuille pour chaque test
                $testSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $user->getLastName() . ' ' . $user->getFirstName());
                $spreadsheet->addSheet($testSheet);
                $spreadsheet->setActiveSheetIndex(1);

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

                    // Récupérez les informations de poids
                    $weight = $this->getDoctrine()->getRepository(Weight::class)->findOneBy(['user' => $user]);

                    // Récupérez les informations de taille
                    $height = $this->getDoctrine()->getRepository(Height::class)->findOneBy(['user' => $user]);

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
                    $testSheet->setCellValue('K' . $row, $weight ? $weight->getValue() : '' . ' kg'); // Si le poids existe, utilisez sa valeur, sinon laissez vide
                    $testSheet->setCellValue('L' . $row, $height ? $height->getValue() : '' . ' cm'); // Si la taille existe, utilisez sa valeur, sinon laissez vide

                    // Continuez à ajouter les données nécessaires dans les colonnes suivantes
                    $testRow++;
                }

                // Sélectionnez la première feuille pour la prochaine itération
                $spreadsheet->setActiveSheetIndex(0);
            }
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
    }

}
