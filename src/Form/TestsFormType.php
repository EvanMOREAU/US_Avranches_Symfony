<?php

namespace App\Form;

use App\Entity\Palier;
use App\Entity\Tests;
use App\Repository\UserRepository;
use App\Form\Type\MinutesSecondesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Time;
use App\Form\DataTransformer\VideoDataTransformer;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class TestsFormType extends AbstractType
{

    public function __construct(UserRepository $userRepository, Security $security, ParameterBagInterface $parameterBag)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->videoDirectory = $parameterBag->get('upload_dir');;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('vma', NumberType::class, [
            'label' => 'VMA',
            'required' => false,
            'attr' => [
                'min' => 0,   // Valeur minimale
                'max' => 20,  // Valeur maximale
            ],
            'constraints' => [
                new Range([
                    'min' => 0,
                    'max' => 20,
                    'minMessage' => 'La VMA ne doit pas être négative.',
                    'maxMessage' => 'La VMA ne doit pas dépasser les 20.',
                ]),
            ],
        ])  
        ->add('demicooper', NumberType::class, [
            'label' => 'Demi-Cooper',
            'required' => false,
            'attr' => [
                'min' => 0,   // Valeur minimale
                'max' => 10000,  // Valeur maximale
            ],
            'constraints' => [
                new Range([
                    'min' => 0,
                    'max' => 100000,
                    'minMessage' => 'La distance parcourue ne doit pas être négative.',
                    'maxMessage' => 'La distance parcourue ne doit pas être supérieure à 100000 mètres.',
                ]),
            ],
        ])       
        ->add('cooper', NumberType::class, [
            'label' => 'Cooper',
            'required' => false,
            'attr' => [
                'min' => 0,   // Valeur minimale
                'max' => 10000,  // Valeur maximale
            ],
            'constraints' => [
                new Range([
                    'min' => 0,
                    'max' => 100000,
                    'minMessage' => 'La distance parcourue ne doit pas être négative.',
                    'maxMessage' => 'La distance parcourue ne doit pas être supérieure à 100000 mètres.',
                ]),
            ],
        ])          
        ->add('jongle_gauche', NumberType::class, [
            'label' => 'Jongle Gauche',
            'required' => false,
            'attr' => [
                'min' => 0,   // Valeur minimale
                'max' => 50,  // Valeur maximale
            ],
            'constraints' => [
                new Range([
                    'min' => 0,
                    'max' => 50,
                    'minMessage' => 'Le nombre de jongles du pied gauche ne doit pas être négatif.',
                    'maxMessage' => 'Le nombre de jongles du pied gauche doit être inférieur à 50.',
                ]),
            ],
        ])
        ->add('jongle_droit', NumberType::class, [
            'label' => 'Jongle Droit',
            'required' => false,
            'attr' => [
                'min' => 0,   // Valeur minimale
                'max' => 50,  // Valeur maximale
            ],
            'constraints' => [
                new Range([
                    'min' => 0,
                    'max' => 50,
                    'minMessage' => 'Le nombre de jongles du pied droit ne doit pas être négatif.',
                    'maxMessage' => 'Le nombre de jongles du pied droit doit être inférieur à 50.',
                ]),
            ],
        ])
        ->add('jongle_tete', NumberType::class, [
            'label' => 'Jongle Tête',
            'required' => false,
            'attr' => [
                'min' => 0,   // Valeur minimale
                'max' => 30,  // Valeur maximale
            ],
            'constraints' => [
                new Range([
                    'min' => 0,
                    'max' => 30,
                    'minMessage' => 'Le nombre de jongles de la tête ne doit pas être négatif.',
                    'maxMessage' => 'Le nombre de jongles de la tête doit être inférieur à 30.',
                ]),
            ],
        ])
        ->add('conduiteballe', TextType::class, [
            'label' => 'Conduite de balle (en millisecondes)',
            'required' => false,
        ])
        ->add('vitesse', TextType::class, [
            'label' => 'Vitesse (en millisecondes)',
            'required' => false,
        ])
        ->add('palier', EntityType::class, [
            'class' => Palier::class,
            'choices' => $options['paliers'],
            'choice_label' => 'name',
            'placeholder' => 'Sélectionner un palier',
            'required' => false,
        ])



        ->add('video', FileType::class, [
            'label' => 'Vidéo',
            'required' => false, // Le champ n'est pas obligatoire lors de la modification
            'constraints' => [
                new File([
                    'maxSize' => '1024M',
                    'mimeTypes' => [
                        'video/*',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid video file',
                ]),
            ],
        ])
        ;
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $builder->add('user', ChoiceType::class, [
                'choices' => $this->getUserChoices(),
                'label' => 'Sélectionner un utilisateur',
                'required' => true,
            ]);
        }
        // Ajoutez le transformateur de données au champ 'video'
        $builder->get('video')
            ->addModelTransformer(new VideoDataTransformer($this->videoDirectory));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tests::class,
            'paliers' => null, // Add this line to define the 'paliers' option
        ]);
    }
    
    private function getUserChoices()
    {
        $users = $this->userRepository->findAll();

        $choices = [];
        foreach ($users as $user) {
            $choices[$user->getFirstName()] = $user;
        }

        return $choices;
    }
}
