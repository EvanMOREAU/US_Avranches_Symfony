<?php

namespace App\Form;

use App\Entity\Tests;
use App\Form\Type\MinutesSecondesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Time;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class TestsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('vma', NumberType::class, [
            'label' => 'VMA',
            'attr' => [
                'min' => 0,   // Valeur minimale
                'max' => 20,  // Valeur maximale
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a VMA value',
                ]),
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
            'attr' => [
                'min' => 0,   // Valeur minimale
                'max' => 10000,  // Valeur maximale
            ],
        ])       
        ->add('cooper', NumberType::class, [
            'label' => 'Cooper',
            'attr' => [
                'min' => 0,   // Valeur minimale
                'max' => 10000,  // Valeur maximale
            ],
        ])          
        ->add('jongle_gauche', NumberType::class, [
            'label' => 'Jongle Gauche',
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
            'label' => 'Conduite de balle (Ex: 5s 300ms)',
            'attr' => ['class' => 'form-input'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tests::class,
        ]);
    }
}
