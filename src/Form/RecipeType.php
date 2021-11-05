<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\FoodAmountType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true
            ])
            ->add('category', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'entrée' => 'entrée',
                    'plat' => 'plat',
                    'dessert' => 'dessert'
                ]
            ])
            ->add('duration', NumberType::class, [
                'required' => true,
            ])
            ->add('persons', NumberType::class, [
                'required' => true,
                'attr' => [
                    'value' => 4
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
