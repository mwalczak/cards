<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Game;
use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class GamePersister implements DataPersisterInterface
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
        return $data instanceof Game;
    }

    /**
     * @param Game $data
     * @return object|void
     */
    public function persist($data)
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        if ($data->getName()) {
            $player = new Player();
            $player->setGame($data);
            $player->setName($data->getName());
            $data->addPlayer($player);
            $this->entityManager->persist($player);
            $this->entityManager->flush();
            $this->logger->notice('Game created (game: ' . $data->getId() . ', player: ' . $player->getName() . ')');
        }
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