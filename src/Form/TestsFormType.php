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
                'min' => 1,   // Valeur minimale
                'max' => 16,  // Valeur maximale
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a VMA value',
                ]),
                new Range([
                    'min' => 1,
                    'max' => 16,
                    'minMessage' => 'The VMA should be at least {{ limit }}',
                    'maxMessage' => 'The VMA should not exceed {{ limit }}',
                ]),
            ],
        ])        
        ->add('cooper', TimeType::class, [
            'label' => 'Cooper (MM:SS)',
            'input' => 'string', // Utiliser l'entrée sous forme de chaîne
            'input_format' => 'h:i:s',
            'widget' => 'single_text', // Utilisation du widget "single_text"
            'with_seconds' => true,
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
        ->add('date', DateTimeType::class, [
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
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tests::class,
        ]);
    }
}
