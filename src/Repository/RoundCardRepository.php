<?php

namespace App\Repository;

use App\Entity\RoundCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RoundCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoundCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoundCard[]    findAll()
 * @method RoundCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoundCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoundCard::class);
    }

    // /**
    //  * @return RoundCard[] Returns an array of RoundCard objects
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
    public function findOneBySomeField($value): ?RoundCard
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
