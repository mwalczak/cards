<?php

namespace App\Repository;

use App\Entity\PlayerCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlayerCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerCard[]    findAll()
 * @method PlayerCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerCard::class);
    }

    // /**
    //  * @return PlayerCard[] Returns an array of PlayerCard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlayerCard
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
