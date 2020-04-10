<?php


namespace App\DataPersister;


use App\Entity\Game;
use App\Entity\Player;
use App\Entity\QuestionCard;
use App\Entity\Round;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class RoundPersister
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
        $questionCard = $this->entityManager->getRepository(QuestionCard::class)->findNotUsedInGame($game);
        //keep 10 cards on players hands

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        $this->logger->notice('Round created (game: ' . $data->getGame()->getId() . ')');
    }

    /**
     * @inheritDoc
     */
    public function remove($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}