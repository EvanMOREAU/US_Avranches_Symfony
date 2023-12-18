<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HeightVerificationService
{
    private $tokenStorage;
    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    public function verifyHeight(): int
    {

        if ($this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();
            $userId = $user->getId();

            $entityManager = $this->entityManager;

            $heightRepository = $entityManager->getRepository(\App\Entity\Height::class);
            $userHeights = $heightRepository->findBy(['user' => $user]);

            if (!empty($userHeights)) {
                $latestHeightEntry = null;

                foreach ($userHeights as $heightEntry) {
                    if ($latestHeightEntry === null || $heightEntry->getDate() > $latestHeightEntry->getDate()) {
                        $latestHeightEntry = $heightEntry;
                    }
                }
            
                $currentDate = new \DateTime();
                $threeMonthsAgo = (new \DateTime())->modify('-3 months');
            
                if ($latestHeightEntry->getDate() > $threeMonthsAgo) {
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
