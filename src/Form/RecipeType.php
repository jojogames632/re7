<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\CookingType;
use App\Entity\Recipe;
use App\Entity\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\FoodAmountType;
use App\Repository\CategoryRepository;
use App\Repository\CookingTypeRepository;
use App\Repository\TypeRepository;
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
            ->add('type', EntityType::class, [
                'required' => true,
                'class' => Type::class,
                'choice_label' => 'name',
                'query_builder' => function (TypeRepository $tr) {
                    return $tr->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                }
            ])
            ->add('cookingType', EntityType::class, [
                'required' => true,
                'class' => CookingType::class,
                'choice_label' => 'name',
                'query_builder' => function (CookingTypeRepository $ctr) {
                    return $ctr->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                }
            ])
            ->add('category', EntityType::class, [
                'required' => true,
                'class' => Category::class,
                'choice_label' => 'name',
                'query_builder' => function (CategoryRepository $cr) {
                    return $cr->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                }
            ])
            ->add('duration', NumberType::class, [
                'required' => true,
                'attr' => [
                    'value' => 1
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
