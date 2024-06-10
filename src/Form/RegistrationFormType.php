<?php

namespace App\Form;

use App\Entity\User;
use App\Form\HeightType;
use App\Form\WeightType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', null, [
                'label' => false, // Cette option supprime le label
            ])
            
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'required' => false,
                'first_options'  => [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control custom-password-field',
                        'placeholder' => 'Entrez votre mot de passe'
                    ]
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Répétez votre mot de passe'
                    ]
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Length([
                        'min' => 12,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/[0-9]/',
                        'message' => 'Votre mot de passe doit contenir au moins 1 chiffre.',
                    ]),
                    new Regex([
                        'pattern' => '/[\W_]/',
                        'message' => 'Votre mot de passe doit contenir au moins 1 caractère spécial.',
                    ]),
                ],
                'label' => false
            ])
            
            ->add('date_naissance', BirthdayType::class, [
                'widget' => 'choice', // Utilisez 'single_text' pour un champ de texte unique au lieu de menus déroulants.
                'format' => 'dd MM yyyy', // Format de la date
                'years' => range(date('Y') - 20, date('Y')), // La plage d'années que vous voulez afficher (ici, les 100 dernières années)
                'label' => false, // Étiquette du champ
                'html5' => true,

            ])
            ->add('first_name', null, [
                'label' => false,
            ])
            ->add('last_name', null, [
                'label' => false,
            ])
            ->add('resp_phone', null, [
                'label' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre adresse email.',
                    ]),
                    new Email([
                        'message' => 'L\'adresse email "{{ value }}" n\'est pas valide.',
                    ]),
                ],
            ])
            
            // // Ajoutez le formulaire Weight
            // ->add('weight', WeightType::class)
            
            // // Ajoutez le formulaire Height
            // ->add('height', HeightType::class)
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
