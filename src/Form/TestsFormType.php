<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Tests;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class TestsFormType extends AbstractType
{

    public function __construct(UserRepository $userRepository, ParameterBagInterface $parameterBag)
    {
        $this->userRepository = $userRepository;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('vma', NumberType::class, [
            'label' => 'VMA',
            'required' => false,
        ])  
        ->add('demicooper', NumberType::class, [
            'label' => 'Demi-Cooper',
            'required' => false,
            'attr' => [
                'min' => 0,   // Valeur minimale
                'max' => 10000,  // Valeur maximale
            ],
<<<<<<< HEAD
=======
            'constraints' => [
                new Range([
                    'min' => 0,
                    'max' => 100000,
                ]),
            ],
>>>>>>> Update-pre-prod
        ])       
        ->add('cooper', NumberType::class, [
            'label' => 'Cooper',
            'required' => false,
            'attr' => [
                'min' => 0,   // Valeur minimale
                'max' => 100000,  // Valeur maximale
            ],
<<<<<<< HEAD
=======
            'constraints' => [
                new Range([
                    'min' => 0,
                    'max' => 100000,
                ]),
            ],
>>>>>>> Update-pre-prod
        ])          
        ->add('jongle_gauche', NumberType::class, [
            'label' => 'Jongle Gauche',
            'required' => false,
        ])
        ->add('jongle_droit', NumberType::class, [
            'label' => 'Jongle Droit',
            'required' => false,
        ])
        ->add('jongle_tete', NumberType::class, [
            'label' => 'Jongle Tête',
            'required' => false,
        ])
        ->add('conduiteballe', TextType::class, [
            'label' => 'Conduite de balle (en millisecondes)',
            'required' => false,
        ])
        ->add('vitesse', TextType::class, [
            'label' => 'Vitesse (en millisecondes)',
            'required' => false,
        ])
        ->add('user', EntityType::class, [
            'class' => User::class,
            'choice_label' => function ($user) {
                return $user->getFirstName() . ' ' . $user->getLastName() . ' - ' . $user->getCategory();
            },
            'label' => 'Utilisateur :',
            'placeholder' => 'Choisir un utilisateur',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->andWhere('u.roles LIKE :role')
                    ->setParameter('role', '%"ROLE_PLAYER"%')
                    ->orderBy('u.first_name', 'ASC')
                    ->addOrderBy('u.last_name', 'ASC');
            },
        ]);
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
