<?php

namespace App\Repository;

use App\Entity\Shopping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Food;

/**
 * @method Shopping|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shopping|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shopping[]    findAll()
 * @method Shopping[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShoppingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shopping::class);
    }

    public function findAllFilteredBySection($owner)
    {
        return $this->createQueryBuilder('s')
            ->where('s.owner = :owner')
            ->setParameter(':owner', $owner)
            ->orderBy('s.section', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByFoodUnitAndOwner($foodId, $unit, $owner)
    {
        return $this->createQueryBuilder('s')
            ->where('s.owner = :owner')
            ->setParameter(':owner', $owner)
            ->andWhere('s.food = :foodId')
            ->setParameter(':foodId', $foodId)
            ->andWhere('s.unit = :unit')
            ->setParameter(':unit', $unit)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
