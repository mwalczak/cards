<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\QuestionCard;
use App\Entity\Round;
use App\Enum\RoundStatus;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class RoundPersister implements DataPersisterInterface
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function supports($data): bool
    {
        return $data instanceof Round;
    }

    /**
     * @param Round $data
     * @return object|void
     */
    public function persist($data)
    {
        //draw question card
        $game = $data->getGame();

        $unfinishedRounds = $this->entityManager->getRepository(Round::class)->findBy([
            'game' => $game,
            'status' => RoundStatus::NEW()
        ]);

        if(count($unfinishedRounds) && $unfinishedRounds[0]->getId()!=$data->getId()){
            throw new BadRequestHttpException('there are unfinished rounds in this game: '.$unfinishedRounds[0]->getId());
        }

        if(!$data->getQuestionCard()){
            /** @var QuestionCard $questionCard */
            $questionCard = $this->entityManager->getRepository(QuestionCard::class)->findRandomOneNotUsed($game->getUsedQuestions());
            $data->setQuestionCard($questionCard);
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        $this->logger->notice('Round created (game: ' . $data->getGame()->getId() . ', card: '.$data->getQuestionCard()->getId().')');
    }

    /**
     * @inheritDoc
     * @param Round $data
     */
    public function remove($data)
    {
        if($data->getStatus() == RoundStatus::FINISHED()){
            throw new BadRequestHttpException('can\'t cancel finished round');
        }
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}