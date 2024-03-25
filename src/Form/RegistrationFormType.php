<?php

namespace App\Form;

use App\Entity\User;
use App\Form\HeightType;
use App\Form\WeightType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

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
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
                'label' => false, // Cette option supprime le label
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
