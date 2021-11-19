<?php

namespace App\Form;

use App\Entity\Food;
use App\Entity\RecipeFood;
use App\Entity\Unit;
use App\Repository\FoodRepository;
use App\Repository\UnitRepository;
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
                'choice_label' => 'name',
                'query_builder' => function (FoodRepository $fr) {
                    return $fr->createQueryBuilder('f')
                        ->orderBy('f.name', 'ASC');
                }
            ])
            ->add('quantity', NumberType::class, [
                'required' => true
            ])
            ->add('unit', EntityType::class, [
                'required' => true,
                'class' => Unit::class,
                'choice_label' => 'name',
                'query_builder' => function (UnitRepository $ur) {
                    return $ur->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                }
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
