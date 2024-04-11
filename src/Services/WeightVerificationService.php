<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class WeightVerificationService
{
    private $tokenStorage;
    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    public function verifyWeight(): int
    {

        if ($this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();
            $userId = $user->getId();

            $entityManager = $this->entityManager;

            $WeightRepository = $entityManager->getRepository(\App\Entity\Weight::class);
            $userWeights = $WeightRepository->findBy(['user' => $user]);

            if (!empty($userWeights)) {
                $latestWeightEntry = null;

                foreach ($userWeights as $WeightEntry) {
                    if ($latestWeightEntry === null || $WeightEntry->getDate() > $latestWeightEntry->getDate()) {
                        $latestWeightEntry = $WeightEntry;
                    }
                }
            
                $currentDate = new \DateTime();
                $threeMonthsAgo = (new \DateTime())->modify('-3 months');
            
                if ($latestWeightEntry->getDate() > $threeMonthsAgo) {
                    return 1; // A une entrée et la dernière est inférieure à 3 mois
                } else {
                    return -1; // A une entrée mais la dernière est supérieure à 3 mois
                }
            }else{
                return 0; // N'a pas d'entrées
            }
        }

        return $userId;

        return 1; // Bon code (1)
    }
}
