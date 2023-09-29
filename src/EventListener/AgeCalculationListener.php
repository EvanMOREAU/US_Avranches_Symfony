<?php

// src/EventListener/AgeCalculationListener.php

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\User;

class AgeCalculationListener
{
    public function prePersist(User $user, LifecycleEventArgs $event)
    {
        $this->calculateAgeAndAssignRole($user);
    }

    public function preUpdate(User $user, LifecycleEventArgs $event)
    {
        $this->calculateAgeAndAssignRole($user);
    }

    private function calculateAgeAndAssignRole(User $user)
    {
        // Calculer l'âge à partir de la date de naissance
        $dateOfBirth = $user->getDateOfBirth();
        $now = new \DateTime();
        $age = $dateOfBirth->diff($now)->y;

        // Assigner le rôle en fonction de l'âge
        if ($age < 18) {
            $user->setRoles(['ROLE_U10']);
        } else {
            $user->setRoles(['ROLE_U13']);
        }
    }
}
