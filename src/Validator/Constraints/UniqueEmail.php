<?php
// src/Validator/Constraints/UniqueEmail.php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueEmail extends Constraint
{
    public $message = 'Cette adresse email est déjà utilisée.';
}
