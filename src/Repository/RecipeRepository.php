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

    public function getRecipesWithTitleAndCategory($title = null, $category = null)
    {
        $query = $this->createQueryBuilder('r');

        if ($title != null) {
            $query->where('r.name LIKE :val')
                ->setParameter(':val', '%'.$title.'%');
        }
        
        if ($category != null) {
            $query->andWhere('r.category = :val')
                ->setParameter(':val', $category);
        }
            $query->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $query->getQuery()->getResult();
    }
}
