<?php
// src/Form/ParameterType.php
namespace App\Form;

use App\Entity\User; // Update this line
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class ParameterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder       
            ->add('username')
            ->add('resp_phone')
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'required' => false,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => ['class' => 'form-control password-field'] // Ajoutez une classe spécifique ici
                ],
                'second_options' => [
                    'label' => 'Répétez le mot de passe',
                    'attr' => ['class' => 'form-control password-field'] // Ajoutez une classe spécifique ici
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
            ])            
            ->add('profile_image', FileType::class, [
                'label' => 'Image de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier image valide',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // Update this line
        ]);
    }
}
