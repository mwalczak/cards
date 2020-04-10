<?php


namespace App\DataPersister;


use App\Entity\Game;
use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class PlayerPersister
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
        return $data instanceof Player;
    }

    /**
     * @param Player $data
     * @return object|void
     */
    public function persist($data)
    {
        if ($code = $data->getGameCode()) {
            $game = $this->entityManager->getRepository(Game::class)->find($code);
            if (!$game) {
                throw new NotFoundHttpException('game not found: ' . $code);
            }
            $data->setGame($game);
        } else {
            throw new BadRequestHttpException('game code must be provided');
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        $this->logger->notice('User created (name: ' . $data->getName() . ', game: ' . $code . ')');
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