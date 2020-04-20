<?php


namespace App\EventListener;


use App\Entity\AnswerCard;
use App\Entity\PlayerCard;
use App\Entity\QuestionCard;
use App\Entity\Round;
use App\Enum\RoundStatus;
use Doctrine\Common\Collections\Collection;
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

    public function postPersist(Round $round, LifecycleEventArgs $args)
    {
        $game = $round->getGame();
        $game->addRound($round);

        $em = $args->getEntityManager();

        if (!$round->getQuestionCard()) {
            /** @var QuestionCard $questionCard */

            $questionCards = $em->getRepository(QuestionCard::class)->findAll();
            shuffle($questionCards);
            do {
                /** @var QuestionCard $questionToUse */
                $questionToUse = array_pop($questionCards);
            } while (!$questionToUse || in_array($questionToUse->getId(), $game->getUsedQuestions()));
            if($questionToUse){
                $round->setQuestionCard($questionToUse);
                $this->logger->notice('Round created (game: ' . $game->getId() . ', round: ' . $round->getId() . ', card: ' . $round->getQuestionCard()->getId() . ')');
            } else {
                throw new BadRequestHttpException('No more questions in game');
            }
        }

        $this->drawCards($em, $round);
        $em->flush();
    }

    private function drawCards(EntityManagerInterface $em, Round $round)
    {
        $game = $round->getGame();
        $cardsInGame = $game->getUsedAnswers();
        $cardsInGame = array_merge($cardsInGame, $game->getPlayersCards());
        sort($cardsInGame);

        $this->logger->notice('Cards used (game: ' . $game->getId() . ', round: ' . $round->getId() . ', cards: ' . implode(',', $cardsInGame) . ')');

        $players = $game->getPlayers();
        /** @var AnswerCard[] $cards */
        $cards = $em->getRepository(AnswerCard::class)->findAll();
        shuffle($cards);
        $this->logger->notice('Cards to give (game: ' . $game->getId() . ', round: ' . $round->getId() . ', cards: '.count($cards).')');

        do {
            $cardGiven = false;
            foreach ($players as $player) {
                do {
                    /** @var AnswerCard $cardToGive */
                    $cardToGive = array_pop($cards);
                } while (!$cardToGive || in_array($cardToGive->getId(), $cardsInGame));

                if ($cardToGive && $player->getCardsCount() < $_ENV['CARDS_COUNT']) {
                    $card = new PlayerCard($player, $cardToGive);
                    $em->persist($card);
                    $player->addCard($card);
                    $this->logger->notice('Card given (game: ' . $game->getId() . ', round: ' . $round->getId() . ', player: ' . $player->getName() . ', card: ' . $cardToGive->getId() . ')');
                    $cardsInGame[] = $cardToGive->getId();
                    $cardGiven = true;
                }
            }
        } while (!empty($cards) && $cardGiven);
    }
}