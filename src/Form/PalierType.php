<?php
// src/Form/PalierType.php

namespace App\Form;

use App\Entity\Palier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PalierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('objectif');

        // Ajouter le champ de suppression (visible uniquement lors de la modification)
        if ($options['is_edit']) {
            $builder->add('delete_palier', CheckboxType::class, [
                'label' => 'Supprimer le palier',
                'required' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Palier::class,
            'is_edit' => false, // Par défaut, ce formulaire est utilisé pour la création
        ]);

        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}

