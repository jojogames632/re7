<?php

namespace App\Form;

use App\Entity\Food;
use App\Entity\RecipeFood;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddFoodToRecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('food', EntityType::class, [
                'required' => true,
                'class' => Food::class,
                'choice_label' => 'name'
            ])
            ->add('quantity', NumberType::class, [
                'required' => true,
                'data' => 1
            ])
            ->add('unit', ChoiceType::class, [
                'required' => true,
                'data' => 'g',
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
