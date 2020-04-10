<?php


namespace App\Generator;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

class UniqIdGenerator extends AbstractIdGenerator
{
    public function generate(EntityManager $em, $entity)
    {
        return uniqid();
    }
}