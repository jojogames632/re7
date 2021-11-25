<?php

namespace App\Repository;

use App\Entity\Bonus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Bonus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bonus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bonus[]    findAll()
 * @method Bonus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BonusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bonus::class);
    }

    public function findOneByFoodUnitAndOwner($foodId, $unit, $owner)
    {
        return $this->createQueryBuilder('b')
            ->where('b.owner = :owner')
            ->setParameter(':owner', $owner)
            ->andWhere('b.food = :foodId')
            ->setParameter(':foodId', $foodId)
            ->andWhere('b.unit = :unit')
            ->setParameter(':unit', $unit)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
