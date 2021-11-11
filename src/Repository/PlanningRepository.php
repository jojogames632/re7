<?php

namespace App\Repository;

use App\Entity\Planning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Planning|null find($id, $lockMode = null, $lockVersion = null)
 * @method Planning|null findOneBy(array $criteria, array $orderBy = null)
 * @method Planning[]    findAll()
 * @method Planning[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planning::class);
    }

    public function findAllOwners()
    {
        return $this->createQueryBuilder('p')
            ->select('p.owner')->distinct()
            ->getQuery()
            ->getResult()
        ;
    }

    public function getPlanningOf($owner)
    {
        return $this->createQueryBuilder('p')
            ->where('p.owner = :val')
            ->setParameter(':val', $owner)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByNameAndOwner($name, $owner)
    {
        return $this->createQueryBuilder('p')
            ->where('p.name = :name')
            ->setParameter(':name', $name)
            ->andWhere('p.owner = :owner')
            ->setParameter(':owner', $owner)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
