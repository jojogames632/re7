<?php

namespace App\Repository;

use App\Entity\RecipeFood;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecipeFood|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecipeFood|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecipeFood[]    findAll()
 * @method RecipeFood[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeFoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecipeFood::class);
    }

    public function findByRecipeFilteredById($recipe)
    {
        return $this->createQueryBuilder('p')
            ->where('p.recipe = :recipe')
            ->setParameter(':recipe', $recipe)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    } 
}
