<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FoodAmountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('food', TextType::class, [
                'required' => true
            ])
            ->add('quantity', NumberType::class, [
                'required' => true
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
            
        ]);
    }
}
