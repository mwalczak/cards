<?php

namespace App\Repository;

use App\Entity\AnswerCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnswerCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnswerCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnswerCard[]    findAll()
 * @method AnswerCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnswerCard::class);
    }

    // /**
    //  * @return AnswerCard[] Returns an array of AnswerCard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AnswerCard
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
