<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrez un mot de passe',
                        ]),
                        new Length([
                            'min' => 12,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            // La contrainte de longueur maximale de 4096 est déjà configurée
                        ]),
                        new Regex([
                            'pattern' => '/[0-9]/',
                            'message' => 'Votre mot de passe doit contenir au moins 1 chiffre.',
                        ]),
                        new Regex([
                            'pattern' => '/[A-Z]/',
                            'message' => 'Votre mot de passe doit contenir au moins 1 lettre majuscule.',
                        ]),
                        new Regex([
                            'pattern' => '/[a-z]/',
                            'message' => 'Votre mot de passe doit contenir au moins 1 lettre minuscule.',
                        ]),
                        new Regex([
                            'pattern' => '/[\W_]/',
                            'message' => 'Votre mot de passe doit contenir au moins 1 caractère spécial.',
                        ]),
                    ],
                    'label' => false,
                    'attr' => ['class' => 'form-control custom-password-field', 'placeholder' => 'Nouveau mot de passe'] // Ajoutez une classe spécifique ici
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => ['class' => 'form-control custom-password-field', 'placeholder' => 'Répétez le mot de passe'] // Ajoutez une classe spécifique ici
                ],
                'invalid_message' => 'The password fields must match.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
