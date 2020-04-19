<?php


namespace App\EventListener;


use App\Entity\AnswerCard;
use App\Entity\PlayerCard;
use App\Entity\Round;
use App\Enum\RoundStatus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class RoundUpdateListener
{
    public function preUpdate(Round $entity, PreUpdateEventArgs $args): void
    {
        if ($args->hasChangedField('status') && $args->getNewValue('status') == RoundStatus::NEW()) {
            throw new BadRequestHttpException('Cannot update round with new status');
        }

        if ($args->hasChangedField('winner') && !$args->hasChangedField('status') && $entity->getStatus() == RoundStatus::FINISHED()) {
            throw new BadRequestHttpException('Cannot update winner on finished round');
        }
    }

    public function postUpdate(Round $round, LifecycleEventArgs $args)
    {
        $this->drawCards($args->getEntityManager(), $round);
    }

    public function postPersist(Round $round, LifecycleEventArgs $args)
    {
        $this->drawCards($args->getEntityManager(), $round);
    }

    private function drawCards(EntityManagerInterface $em, Round $round)
    {
        $game = $round->getGame();
        $usedAnswers = $game->getUsedAnswers();
        $cardCount = $_ENV['CARDS_COUNT'] * count($game->getPlayers()) - count($usedAnswers);
        $newCards = $em->getRepository(AnswerCard::class)->findRandomOneNotUsed($usedAnswers, $cardCount);
        $newCardsPerPlayer = floor(count($newCards) / count($game->getPlayers()));
        foreach ($newCards as $card) {
            foreach ($game->getPlayers() as $player) {
                $playerGiven = 0;
                while ($playerGiven < $newCardsPerPlayer && $player->getCardsCount() < $_ENV['CARDS_COUNT']) {
                    $card = new PlayerCard($player, $card);
                    $em->persist($card);
                    $player->addCard($card);
                }
            }
        }

        $em->flush();
    }
}