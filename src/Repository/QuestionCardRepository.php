<?php

namespace App\Repository;

use App\Entity\QuestionCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuestionCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionCard[]    findAll()
 * @method QuestionCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionCard::class);
    }

    public function findRandomOneNotUsed(array $ids): QuestionCard
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.id NOT IN (:ids)')
            ->setParameter('ids', implode(',', $ids))
            ->orderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?QuestionCard
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
