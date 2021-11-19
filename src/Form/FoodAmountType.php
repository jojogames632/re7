<?php

namespace App\Form;

use App\Entity\Unit;
use App\Repository\UnitRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            
        ]);
    }
}
