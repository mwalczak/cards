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

    /**
     * @param array $ids
     * @param int $count
     * @return AnswerCard[]|null
     */
    public function findRandomOneNotUsed(array $ids, $count = 1): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id NOT IN (:ids)')
            ->setParameter('ids', implode(',', $ids))
            ->orderBy('RAND()')
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
            ;
    }

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
