<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findAllAsc()
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getRecipesWithTitle($title)
    {
        return $this->createQueryBuilder('r')
            ->where('r.name LIKE :title')
            ->setParameter(':title', '%'.$title.'%')
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getRecipesWithFilters($category = null, $cookingType = null, $type = null)
    {
        $query = $this->createQueryBuilder('r');

        if ($category != null) {
            $query->where('r.category = :category')
            ->setParameter(':category', $category);
        }
        if ($cookingType != null) {
            $query->andWhere('r.cookingType = :cookingType')
            ->setParameter(':cookingType', $cookingType);
        } 
        if ($type != null) {
            $query->andWhere('r.recipeType = :type')
            ->setParameter(':type', $type);
        } 
            
        return $query->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
