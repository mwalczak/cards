<?php


namespace App\EventListener;


use App\Entity\Round;
use App\Enum\RoundStatus;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class RoundUpdateListener
{
    public function preUpdate(Round $entity, PreUpdateEventArgs $args): void
    {
        if($args->hasChangedField('status') && $args->getNewValue('status') == RoundStatus::NEW()){
            throw new BadRequestHttpException('Cannot update round with new status');
        }

        if ($args->hasChangedField('winner') && $entity->getStatus() == RoundStatus::FINISHED()) {
            throw new BadRequestHttpException('Cannot update winner on finished round');
        }
    }
}