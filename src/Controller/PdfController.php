<?php

namespace App\Controller;

use App\Entity\Pdf;
use App\Entity\User;
use App\Entity\Weight;
use App\Repository\UserRepository;
use App\Repository\TestsRepository;
use App\Service\UserVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[Route('/pdf', name: 'app_pdf')]
class PdfController extends AbstractController
{
    private $userVerificationService;

    public function __construct(UserVerificationService $userVerificationService)
    {
        $this->userVerificationService = $userVerificationService;
    }

    #[Route('/', name: 'app_pdf_index')]
    public function pdf(Request $request, UserRepository $userRepository, TestsRepository $testsRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'ID de l'utilisateur à partir de la route
        $userId = $request->attributes->get('userId');

        // Vérifiez si l'utilisateur a le rôle ROLE_COACH


        if (!$this->userVerificationService->verifyUser()) {
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        // Récupérez le token d'authentification de l'utilisateur actuellement connecté.
        $token = $this->get('security.token_storage')->getToken();

        // Vérifiez le rôle de l'utilisateur
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            // L'utilisateur est super admin, vérifiez s'il a sélectionné un autre utilisateur
            $selectedUserId = $request->query->get('userId'); // Use 'userId' as the parameter name

            if ($selectedUserId) {
                $selectedUser = $userRepository->find($selectedUserId);

                if (!$selectedUser) {
                    throw $this->createNotFoundException('Utilisateur non trouvé');
                }

                $user = $selectedUser;
            }
        } elseif ($token instanceof TokenInterface) {
            // Si ce n'est pas un super admin, utilisez l'utilisateur du token
            $user = $token->getUser();
        }

        // Créez une nouvelle instance de la classe PDF.
        $pdf = new Pdf();

        if ($token instanceof TokenInterface) {
            // Récupérez l'utilisateur à partir du token d'authentification.
            $user = $token->getUser();

            if ($user instanceof User) {

                $tests = $testsRepository->findBy(['user' => $user]);

                // Récupérez les tests triés par date décroissante
                $tests = $testsRepository->findBy(['user' => $user], ['date' => 'DESC']);

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
                $pdf->MultiCell(70, 10, $user->getFirstName() . ' ' . $user->getLastName(), 0, 'C', 0, 1, '', '', true);

                // Configuration de la police et des couleurs pour le contenu du joueur
                $pdf->SetFont('helvetica', 'B', 20);
                $pdf->SetTextColor(0, 0, 0);

                // Contenu du joueur (avec HTML)
                $contentInfos = '
                    <style>.link { color: rgb(42, 56, 114) }</style>
                    <br><br><br>
                    <b><i>  Informations du joueur : </i></b>
                    <br><br>
                    <p><b>Date de naissance : </b>' . $user->getDateNaissance()->format('d/m/Y') . '
                    <br><hr><br><div></div>
                    <b>Catégorie : </b>' . $user->getCategory() . '
                    <br><hr><br><div></div>
                    <b>Nombre de matchs joués :</b> 2
                    <br><hr><br><div></div>
                    </p>
                    <p><b> Contact :</b>
                    <br> Christelle DELARUE<br>
                    <br>
                    Club House US Avranches MSM<br>
                    Allée Jacques Anquetil<br>
                    50300 Avranches.<br><br>
                    <b>Téléphone :</b> 02.33.48.30.78 <br><br>
                    <b>Mails :</b><br>
                    <span class="link"><u>communication@us-avranches.fr</u></span><br>
                    <span class="link"><u>partenaires@us-avranches.fr</u></span><br>
                    <span class="link"><u>us.avranches@orange.fr</u></span>
                    </p>';

                // Ajout du contenu du joueur au PDF
                $pdf->SetFont('helvetica', '', 10);
                $pdf->writeHTMLCell(65, 230, '', '', $contentInfos, 0, 0, 0, true, '', true);

                $profileImagePath = 'uploads/images/' . $user->getId() . '.jpg';

                if (file_exists($profileImagePath)) {
                    // L'image existe, utilisez-la
                    $pdf->Image($profileImagePath, 130, 33.3, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);
                } else {
                    // Utilisez une image anonyme
                    $pdf->Image('img/anonyme.jpg', 130, 33.3, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);
                }

                foreach ($tests as $test) {

                    // Ajout d'une nouvelle page pour chaque test
                    $pdf->AddPage();
                    $pdf->setJPEGQuality(75);

                    // Calcul des dimensions de la page
                    $largeurPage = $pdf->getPageWidth() + 30;
                    $hauteurPage = $pdf->getPageHeight() - 25;

                    $pdf->SetFontSize(16); // Définir la taille de police à 16 points (ajustez selon vos besoins)
                    $pdf->MultiCell(70, 10, $user->getFirstName() . ' ' . $user->getLastName(), 0, 'C', 0, 1, '', '', true);
                    $pdf->SetFontSize(10); // Rétablir la taille de police à la valeur par défaut (si nécessaire)

                    // --- Contenu du pdf ---
                    $contentTests = '<br><br><br>';
                    // Afficher les poids
                    $weights = $this->getWeightsForUser($user, $entityManager);
                    foreach ($weights as $weight) {
                        // Utilisez la fonction pour récupérer la date du poids la plus proche
                        // $nearestWeightDate = $this->getNearestWeightDate($user, $test->getDate(), $entityManager);
                        $date = $weight->getDate();
                        $contentTests .= '<br><hr><br><div></div>';
                        $contentTests .= '<b>Poids le </b>';

                        //Affichez la date du poids sur le PDF si elle est disponible
                        if ($date) {
                            $formatted_date = $date->format("d-m-Y");

                            $contentTests .= '<b>' . $formatted_date . ' :</b> ' . $weight->getValue() . ' kg';
                        } else {
                            $contentTests .= "fail";
                        }
                        // } else {
                        //     $contentTests .= $weight->getValue() . ' kg';
                        // }
                        // Affichez la date du poids sur le PDF si elle est disponible
                        // if ($nearestWeightDate) {
                        //     $contentTests .= '<b>' . $nearestWeightDate->format('d/m/Y') . ' :</b> ' . $weight->getValue() . ' kg';
                        // } else {
                        //     $contentTests .= $weight->getValue() . ' kg';
                        // }
                        // N'affichez qu'une seule fois, car vous avez déjà récupéré tous les poids en dehors de cette boucle
                        break;
                    }
                    $contentTests .= '<br><hr><br><div></div>
                    <b>Taille :</b> 173 cm
                    <br><hr><br><div></div>
                    <p><b>VMA : </b>' . $test->getVma() . ' km/h 
                    <br><hr><br><div></div>
                    <b>Cooper : </b>' . $test->getCooper() . ' mètres
                    <br><hr><br><div></div>
                    <b>Demi-cooper : </b>' . $test->getDemiCooper() . ' mètres
                    <br><hr><br><div></div>
                    <b>Jongles pied gauche : </b>' . $test->getJongleGauche() . ' 
                    <br><hr><br><div></div>
                    <b>Jongles pied droit : </b>' . $test->getJongleDroit() . ' 
                    <br><hr><br><div></div>
                    <b>Jongles tête : </b>' . $test->getJongleTete() . ' 
                    <br><hr><br><div></div>
                    <b>Date des tests : </b>' . $test->getDate()->format('d/m/Y') . ' 
                    <br><hr><br><div></div>
                    <b>Conduite de balle : </b>' . $test->getConduiteBalle() . ' secondes
                    <br><hr><br><div></div>
                    <b>Vitesse : </b>' . $test->getVitesse() . ' secondes
                    </p>';

                    $pdf->writeHTMLCell(65, 230, '', '', $contentTests, 0, 0, 0, true, '', true);
                    // Ajout d'une image au PDF
                    $pdf->Image('img/graph_' . $user->getFirstName() . '.jpg', 95, 150, 100, 100, '', '', '', false, 300, '', false, false, 1, false, false, false);

                    // Insérer la date au dessus de l'image et faire en sorte qu'elle soit bien visible.
                    $posX = 145;
                    $posY = 33.3;
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->SetFontSize(20);
                    $pdf->MultiCell($posX, $posY, $test->getDate()->format('d/m/Y, H:i:s'), 0, 'C', 0, 1, '', '', true);
                    $pdf->SetFontSize(10);
                    $pdf->SetTextColor(0, 0, 0);

                    $pdf->Image('img/joueur.jpg', 130, $posY, 40, 45, '', '', '', false, 300, '', false, false, 1, false, false, false);

                }

                // Génération du PDF et envoi en réponse
                ob_clean(); // Efface la sortie tampon
                $pdfContent = $pdf->Output('US-Avranches-' . '.pdf', 'S');

                return new Response($pdfContent, Response::HTTP_OK, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="US-Avranches.pdf"',
                ]);
            }
        }

        // Gestion des cas d'erreur
        return new Response('Erreur');
    }

    private function getWeightsForUser(User $user, EntityManagerInterface $entityManager): array
    {
        // Utilisez le repository de l'entité Weight pour récupérer tous les poids triés par date
        $weights = $entityManager->getRepository(Weight::class)->findBy(['user' => $user], ['date' => 'ASC']);

        return $weights;
    }

    private function getNearestWeightDate(User $user, \DateTimeInterface $testDate, EntityManagerInterface $entityManager): ?\DateTimeInterface
    {
        // Utilisez le repository de l'entité Weight
        $queryBuilder = $entityManager->createQueryBuilder();

        $nearestWeightDate = $queryBuilder
            ->select('w.date')
            ->from(Weight::class, 'w')
            ->where('w.user = :user')
            ->andWhere('w.date <= :testDate')
            ->setParameter('user', $user)
            ->setParameter('testDate', $testDate)
            ->orderBy('w.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_SINGLE_SCALAR);

        return $nearestWeightDate ? new \DateTimeImmutable($nearestWeightDate) : null;
    }

    #[Route('/list-players', name: 'app_pdf_list_players')]
    public function listPlayers(UserRepository $userRepository): Response
    {
        // Récupérez la liste des utilisateurs ayant le rôle ROLE_PLAYER
        $players = $userRepository->findByRole('ROLE_PLAYER');

        // Affichez la liste des joueurs dans une vue
        return $this->render('pdf/list.players.html.twig', [
            'players' => $players,
            'location' => 'f',
        ]);
    }

    #[Route('/{userId}', name: 'app_pdf_view_pdf')]
    public function viewPdf(int $userId, Request $request, UserRepository $userRepository, TestsRepository $testsRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérez l'utilisateur
        $user = $userRepository->find($userId);

        // Vérifiez si l'utilisateur existe et a le rôle ROLE_PLAYER
        if (!$user || !in_array('ROLE_PLAYER', $user->getRoles(), true)) {
            // Redirigez vers une page d'erreur ou affichez un message d'erreur
            throw $this->createNotFoundException('Utilisateur non trouvé ou n\'a pas le rôle de joueur.');
        }

        // Créez une nouvelle instance de la classe Request avec les paramètres appropriés
        $request = new Request([], [], ['userId' => $userId]);

        // Appel de la méthode pdf avec le nouvel objet Request
        $pdfResponse = $this->pdf($request, $userRepository, $testsRepository, $entityManager);

        // Retournez la réponse du PDF
        return $pdfResponse;
    }

}