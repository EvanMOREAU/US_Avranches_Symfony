<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserVerificationService
{
    private $tokenStorage;
    private $router;

    public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router)
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    public function verifyUser(): int
    {

        if ($this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();

            if ($user && !$user->isIsCodeValidated()) {
               return 0; // Pas le droit (0)
            }
        } elseif(!$this->tokenStorage->getToken()){
            return -1; // Pas connecté (-1)
        }
        

        return 1; // Bon code (1)
    }
}
