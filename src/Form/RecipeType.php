<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\FoodAmountType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true
            ])
            ->add('type', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Entrée' => 'Entrée',
                    'Plat' => 'Plat',
                    'Dessert' => 'Dessert',
                    'Sauce' => 'Sauce',
                    'Autre' => 'Autre'
                ]
            ])
            ->add('category', EntityType::class, [
                'required' => true,
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('cookingType', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Robot cuiseur' => 'Robot cuiseur',
                    'Four' => 'Four',
                    'Sans cuisson' => 'Sans cuisson',
                    'Vapeur' => 'Vapeur',
                    'Gaz' => 'Gaz',
                    'Autocuiseur' => 'Autocuiseur',
                    'Autre' => 'Autre'
                ]
            ])
            ->add('duration', NumberType::class, [
                'required' => true,
                'attr' => [
                    'value' => 40
                ]
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
