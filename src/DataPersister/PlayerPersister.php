<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class PlayerPersister implements DataPersisterInterface
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
        if (!$data->getGame()) {
            throw new BadRequestHttpException('game code must be provided');
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        $this->logger->notice('Player created (name: ' . $data->getName() . ', game: ' . $data->getGame()->getId() . ')');
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