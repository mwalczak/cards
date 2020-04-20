<?php


namespace App\EventListener;


use App\Entity\AnswerCard;
use App\Entity\PlayerCard;
use App\Entity\Round;
use App\Enum\RoundStatus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class RoundUpdateListener
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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
        if($round->getGame()->getRoundsCount()==0){
            $this->drawCards($args->getEntityManager(), $round);
        }
    }

    private function drawCards(EntityManagerInterface $em, Round $round)
    {
        $game = $round->getGame();
        $players = $game->getPlayers();
        $limit = $_ENV['CARDS_COUNT'] * count($players);
        $newCards = $em->getRepository(AnswerCard::class)->findRandomOneNotUsed($game->getUsedAnswers(), $limit);

        $cardsGiven = true;
        while(!empty($newCards) && $cardsGiven){
            $cardsGiven = false;
            foreach ($players as $player) {
                if(!empty($newCards) && $player->getCardsCount() < $_ENV['CARDS_COUNT']){
                    $cardToGive = array_shift($newCards);
                    $card = new PlayerCard($player, $cardToGive);
                    $em->persist($card);
                    $player->addCard($card);
                    $this->logger->notice('Card given (game: ' . $game->getId() . ', player: ' . $player->getName() . ', card: '.$cardToGive->getId().')');
                    $cardsGiven = true;
                }
            }
        }

        $em->flush();
    }
}