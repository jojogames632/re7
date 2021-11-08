<?php

namespace App\Form;

use App\Entity\RecipeFood;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateFoodInRecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', NumberType::class, [
                'required' => true,
            ])
            ->add('unit', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'kg' => 'kg',
                    'g' => 'g',
                    'cl' => 'cl',
                    'l' => 'l',
                    'pièce' => 'pièce',
                    'pincée' => 'pincée',
                    'poignée' => 'poignée'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeFood::class,
        ]);
    }
}
