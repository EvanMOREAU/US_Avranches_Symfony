<?php

namespace App\Controller;

use DateTime;
use App\Entity\Tests;
use App\Entity\Height;
use App\Entity\Weight;
use Doctrine\ORM\EntityManager;
use App\Entity\ChartConfiguration;
use App\Repository\UserRepository;
use App\Repository\PalierRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\UserVerificationService;
use App\Services\HeightVerificationService;
use App\Services\WeightVerificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ChartConfigurationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/charts')]
class ChartsController extends AbstractController
{
    private $userVerificationService;
    private $heightVerificationService;
    private $weightVerificationService;

    public function __construct(UserVerificationService $userVerificationService, HeightVerificationService $heightVerificationService, WeightVerificationService $weightVerificationService)
    {
        $this->userVerificationService = $userVerificationService;
        $this->heightVerificationService = $heightVerificationService;
        $this->weightVerificationService = $weightVerificationService; 
    }
    
    #[Route('/details', name: 'app_charts_details', methods: ['GET'])]
    public function index(ChartConfigurationRepository $configRepository, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PLAYER');

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        $userVerif = $this->userVerificationService->verifyUser();
        $heightVerif = $this->heightVerificationService->verifyHeight();
        $weightVerif = $this->weightVerificationService->verifyWeight();
        // Rediriger si l'utilisateur n'est pas authentifié
        if (!$user) {
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $chartData = [];

        // Récupérer toutes les configurations de graphique
        $configurations = $configRepository->findAll();

        // Parcourir les configurations de graphique
        foreach ($configurations as $config) {
            // Récupérer les données spécifiques en fonction de l'entité configurée
            $entity = $config->getConfigData()['entity'];
            $data = [];
            if ($entity === 'App\Entity\Height') {
                $data = $entityManager->getRepository(Height::class)->findBy(['user' => $user]);
            } elseif ($entity === 'App\Entity\Weight') {
                $data = $entityManager->getRepository(Weight::class)->findBy(['user' => $user]);
            } elseif ($entity === 'App\Entity\Tests') {
                $data = $entityManager->getRepository(Tests::class)->findBy(['user' => $user]);
            }

            // Générer les données du graphique
            $chartData[$config->getId()] = [
                'name' => $config->getName(),
                'chartType' => $config->getChartType(),
                'data' => $this->generateLineChartData($data, $config->getConfigData()['field']),
                'min' => $config->getConfigData()['min'],
                'max' => $config->getConfigData()['max'],
                'paliermin' => $config->getConfigData()['paliermin'],
                'paliermax' => $config->getConfigData()['paliermax'],
            ];
        }
        $players = $userRepository->findByRole('ROLE_PLAYER');
        if($userVerif == 0 ){return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);}
            else if($userVerif == -1) {return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);} 
            else if($userVerif == 1) {
                if($heightVerif == -1){return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);}
                else if($heightVerif == 0){return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);}
                else if($heightVerif == 1){
                    if($weightVerif == -1){return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);}
                    else if($weightVerif == 0){return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);}
                    else if($weightVerif == 1){
                        return $this->render('charts/index.html.twig', [
                            'players' => $players,
                            'chartData' => $chartData,
                            'location' => 'b',
                        ]);
                    }
                }
            }

        // pour la partie coach
        // Récupérez la liste des utilisateurs ayant le rôle ROLE_PLAYER
    }

    #[Route('/', name: 'app_charts_index', methods: ['GET'])]
    public function test(ChartConfigurationRepository $configRepository, EntityManagerInterface $entityManager, PalierRepository $palierRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $user = $this->getUser();

        $userVerif = $this->userVerificationService->verifyUser();
        $heightVerif = $this->heightVerificationService->verifyHeight();
        $weightVerif = $this->weightVerificationService->verifyWeight();

        if (!$user) {
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $totalDataCount = $entityManager->getRepository(Height::class)->count(['user' => $user]) + 
                        $entityManager->getRepository(Weight::class)->count(['user' => $user]) + 
                        $entityManager->getRepository(Tests::class)->count(['user' => $user]);
        $currentMonth = date('m');
        $currentYear = date('Y');

        $totalDataCountThisMonth = 0;
        
        $earliestDate = null;
        $latestDate = null;
        $earliestDateWithType = null;
        $latestDateWithType = null;
        $sixLastRecord = $this->getLastSixRecordsForUser($entityManager);
        foreach (['Height', 'Weight', 'Tests'] as $entityClass) {
            $entities = $entityManager->getRepository('App\\Entity\\'.$entityClass)->findBy(['user' => $user]);
            
            foreach ($entities as $entity) {
                $entityDate = $entity->getDate(); 
                if ($entityDate->format('m') == $currentMonth && $entityDate->format('Y') == $currentYear) {
                    $totalDataCountThisMonth++;
                }
                if ($earliestDate === null || $entityDate < $earliestDate) {
                    $earliestDate = $entityDate;
                    $earliestDateWithType = $entityClass;

                }
                if ($latestDate === null || $entityDate > $latestDate) {
                    $latestDate = $entityDate;
                    $latestDateWithType = $entityClass; 

                }
            }
        }
        $allowedIds = [1, 2, 7];
        $configurations = $configRepository->findById($allowedIds);
        $chartData = [];
        $currentPalierNumber =  $user->getPalier()->getNumero();

        foreach ($configurations as $config) {
            $entity = $config->getConfigData()['entity'];
            $data = [];

            if ($entity === 'App\Entity\Height') {
                $data = $entityManager->getRepository(Height::class)->findBy(['user' => $user]);
            } elseif ($entity === 'App\Entity\Weight') {
                $data = $entityManager->getRepository(Weight::class)->findBy(['user' => $user]);
            } elseif ($entity === 'App\Entity\Tests') {
                $data = $entityManager->getRepository(Tests::class)->findBy(['user' => $user]);
            }

            $chartData[$config->getId()] = [
                'name' => $config->getName(),
                'chartType' => $config->getChartType(),
                'data' => $this->generateLineChartData($data, $config->getConfigData()['field']),
                'min' => $config->getConfigData()['min'],
                'max' => $config->getConfigData()['max'],
            ];
        }
        if($userVerif == 0 ){return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);}
        else if($userVerif == -1) {return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);} 
        else if($userVerif == 1) {
            if($heightVerif == -1){return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);}
            else if($heightVerif == 0){return $this->redirectToRoute('app_height_new', [], Response::HTTP_SEE_OTHER);}
            else if($heightVerif == 1){
                if($weightVerif == -1){return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);}
                else if($weightVerif == 0){return $this->redirectToRoute('app_weight_new', [], Response::HTTP_SEE_OTHER);}
                else if($weightVerif == 1){
                    // dump($chartData);
                    return $this->render('charts/test.html.twig', [
                        'chartData' => $chartData,
                        'location' => 'b',
                        'totalDataCount' => $totalDataCount,
                        'totalDataCountThisMonth' => $totalDataCountThisMonth,
                        'earliestDate' => $earliestDate,
                        'latestDate' => $latestDate,
                        'earliestDateWithType' => $earliestDateWithType,
                        'latestDateWithType' => $latestDateWithType,
                        'paliers' => $palierRepository->findInRange($currentPalierNumber - 2, $currentPalierNumber + 2),
                        'sixLastRecord' => $sixLastRecord,

                    ]);
                }
            }
        }
    }

    private function generateLineChartData($data, $field)
    {
        $labels = [];
        $values = [];
    
        // Parcourir les données pour générer les données du graphique en ligne
        foreach ($data as $item) {
            $labels[] = $item->getDate()->format('d-m-Y');
            // Utiliser la méthode get avec le nom du champ spécifié dans la configuration
            $values[] = $item->{'get' . ucfirst($field)}();
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getLastSixRecordsForUser($entityManager)
    {
        
        $user = $this->getUser();
        if (!$user) {
            return null;
        }

        $records = [];

        $weightRecords = $entityManager->getRepository(Weight::class)->findBy(['user' => $user], ['date' => 'DESC'], 6);
        foreach ($weightRecords as $record) {
            $record->type = 'Weight';
            $records[] = $record;
        }
    
        $heightRecords = $entityManager->getRepository(Height::class)->findBy(['user' => $user], ['date' => 'DESC'], 6);
        foreach ($heightRecords as $record) {
            $record->type = 'Height';
            $records[] = $record;
        }
    
        $testRecords = $entityManager->getRepository(Tests::class)->findBy(['user' => $user], ['date' => 'DESC'], 6);
        foreach ($testRecords as $record) {
            $record->type = 'Tests';
            $records[] = $record;
        }
    
        usort($records, function($a, $b) {
            return $a->getDate()->getTimestamp() - $b->getDate()->getTimestamp();
        });
    
        $lastSixRecords = array_slice($records, -6);
    
        return $lastSixRecords;
    }
    
    #[Route('/updatescale', name: 'app_charts_update_scale', methods: ['POST'])]
    public function updateChartScale(Request $request, EntityManagerInterface $entityManager): Response 
    {
        // Récupérer l'ID du graphique et les nouvelles valeurs d'échelle du formulaire
        $chartId = $request->request->get('chartId');
        $newMin = $request->request->get('NewMin');
        $newMax = $request->request->get('NewMax');

        // Récupérer la configuration du graphique
        $chartConfig = $entityManager->getRepository(ChartConfiguration::class)->find($chartId);
        if (!$chartConfig) {
            throw $this->createNotFoundException('Chart configuration not found');
        }

        // Stocker les anciennes valeurs
        $oldMin = $chartConfig->getConfigData()['min'];
        $oldMax = $chartConfig->getConfigData()['max'];
        // ca ca sert a faire le debogage

       // MAJ des valeurs d'échelle 
        $configData = $chartConfig->getConfigData();
        $configData['min'] = $newMin;
        $configData['max'] = $newMax;
        $chartConfig->setConfigData($configData);

        // on fous les modifs dans la base de données
        $entityManager->flush();

        // Rediriger vers la page précédente
        return $this->redirectToRoute('app_charts_details');
 
    }

    #[Route('/updatepalier', name: 'app_charts_update_palier', methods: ['POST'])]
    public function updateChartPalier(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'ID du graphique et les nouvelles valeurs d'échelle du formulaire
        $chartId = $request->request->get('chartId');
        $newPalierMin = $request->request->get('NewPalierMin');
        $newPalierMax = $request->request->get('NewPalierMax');

        // Récupérer la configuration du graphique
        $chartConfig = $entityManager->getRepository(ChartConfiguration::class)->find($chartId);
        if (!$chartConfig) {
            throw $this->createNotFoundException('Chart configuration not found');
        }

        // Stocker les anciennes valeurs
        $oldMin = $chartConfig->getConfigData()['paliermin'];
        $oldMax = $chartConfig->getConfigData()['paliermax'];
        // ca ca sert a faire le debogage

       // MAJ des valeurs d'échelle 
        $configData = $chartConfig->getConfigData();
        $configData['paliermin'] = $newPalierMin;
        $configData['paliermax'] = $newPalierMax;
        $chartConfig->setConfigData($configData);

        // on fous les modifs dans la base de données
        $entityManager->flush();

        // Rediriger vers la page précédente
        return $this->redirectToRoute('app_charts_details');
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route('/details/{userId}', name: 'app_charts_show', methods: ['GET'])]
    public function show(int $userId, ChartConfigurationRepository $configRepository, EntityManagerInterface $entityManager, UserRepository $userRepository): Response {
        // Vérifie si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_COACH')) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette page !');
        }

        // Récupérer l'utilisateur spécifique par son ID
        $user = $userRepository->find($userId);

        // Rediriger si l'utilisateur n'est pas trouvé
        if (!$user) {
            return $this->redirectToRoute('app_charts_index', [], Response::HTTP_SEE_OTHER);
        }

        // Vous pouvez mettre ici la logique pour vérifier l'accès de l'utilisateur coach à ce joueur

        $chartData = [];

        // Récupérer toutes les configurations de graphique
        $configurations = $configRepository->findAll();

        // Parcourir les configurations de graphique
        foreach ($configurations as $config) {
            // Récupérer les données spécifiques en fonction de l'entité configurée
            $entity = $config->getConfigData()['entity'];
            $data = [];
            if ($entity === 'App\Entity\Height') {
                $data = $entityManager->getRepository(Height::class)->findBy(['user' => $user]);
            } elseif ($entity === 'App\Entity\Weight') {
                $data = $entityManager->getRepository(Weight::class)->findBy(['user' => $user]);
            } elseif ($entity === 'App\Entity\Tests') {
                $data = $entityManager->getRepository(Tests::class)->findBy(['user' => $user]);
            }

            // Générer les données du graphique
            $chartData[$config->getId()] = [
                'name' => $config->getName(),
                'chartType' => $config->getChartType(),
                'data' => $this->generateLineChartData($data, $config->getConfigData()['field']),
                'min' => $config->getConfigData()['min'],
                'max' => $config->getConfigData()['max'],
                'paliermin' => $config->getConfigData()['paliermin'],
                'paliermax' => $config->getConfigData()['paliermax'],
            ];
        }

        return $this->render('charts/show.html.twig', [
            'user' => $user,
            'chartData' => $chartData,
            'location' => 'b',
        ]);
    }
}
