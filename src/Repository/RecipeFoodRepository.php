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

    // /**
    //  * @return RecipeFood[] Returns an array of RecipeFood objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RecipeFood
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
