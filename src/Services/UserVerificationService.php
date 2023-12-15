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

    public function verifyUser(): bool
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user && !$user->isIsCodeValidated()) {
           return false;
        }

        return true;
    }
}

// <?php

// namespace App\Service;

// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\RouterInterface;
// use Symfony\Component\HttpFoundation\RedirectResponse;
// use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
// use Symfony\Component\Security\Core\Security;

// class UserVerificationService
// {
//     private $tokenStorage;
//     private $router;
//     private $security;

//     public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router, Security $security)
//     {
//         $this->tokenStorage = $tokenStorage;
//         $this->router = $router;
//         $this->security = $security;
//     }

//     public function verifyUser(): bool
//     {
//         $user = $this->tokenStorage->getToken()->getUser();

//         if ($user && !$user->isIsCodeValidated()) {
//            return false;
//         }

//         return true;
//     }
// }