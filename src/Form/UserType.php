<?php
namespace App\Form;

use App\Entity\User;
use App\Entity\Equipe;
use App\Entity\Palier;

use App\Entity\Nationality;
use App\Repository\EquipeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];

        $builder
            ->add('username')
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
                'constraints' => $isEdit ? [] : [
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
            ->add('equipe', EntityType::class, [
                'class' => Equipe::class,
                'query_builder' => function (EquipeRepository $er) use ($options) {
                    $category = $options['category'];
                    return $er->createQueryBuilder('e')
                        ->leftJoin('e.category', 'c')
                        ->andWhere('c.id = :category_id')
                        ->setParameter('category_id', $category);
                },
                'choice_label' => 'name',
            ])
            ->add('first_name')
            ->add('last_name')
            ->add('resp_phone')
            ->add('classement', ChoiceType::class, [
                'label' => 'Surclassement / Sous-classement',
                'choices' => [
                    'Dans la moyenne' => Null,
                    'Sous-classé(e) d\'un an' => -1,
                    'Surclassé(e) d\'un an' => 1,
                    'Surclassé(e) de deux ans' => 2,
                ],
                'placeholder' => 'Choisir un classement', // Optional placeholder
                'required' => false, // Optional, set to true if the field is required
                'attr' => [
                    'class' => 'form-control', // Optional CSS class
                ],
            ])
            ->add('date_naissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text', // Affichage en un seul champ de texte
                'html5' => false, // Utiliser le type HTML standard
                'attr' => [
                    'class' => 'form-control date-select', // Classe pour Flatpickr
                    'placeholder' => 'YYYY-MM-DD', // Placeholder pour le format de date
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
                        'mimeTypesMessage' => 'Please upload a valid image file',
                    ])
                ],
            ])
            ->add('nationality', EntityType::class, [
                'class' => Nationality::class,
                'choice_label' => 'name', 
                'label' => 'Choose a nationality',
                'placeholder' => 'Sélectionnez une Nationalité', 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'exclude_date_naissance' => false,
            'category' => false,
            'is_edit' => false,  // Ajouter une option par défaut pour distinguer création et édition
        ]);
    }
}

