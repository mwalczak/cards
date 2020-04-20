<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\PlayerCard;
use App\Entity\RoundCard;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class RoundCardPersister implements DataPersisterInterface
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
        return $data instanceof RoundCard;
    }

    /**
     * @param RoundCard $data
     * @return object|void
     */
    public function persist($data)
    {
        try {
            $player = $data->getPlayer();
            if (!$player || !$data->getCard()) {
                throw new BadRequestHttpException('player and card must be provided');
            }
            if (!in_array($data->getCard()->getId(), $player->getCardsIds())) {
                throw new BadRequestHttpException('bad card played');
            }

            $data->getRound()->addAnswerCard($data);

            /** @var PlayerCard $playerCard */
            $playerCard = $this->entityManager->getRepository(PlayerCard::class)->findOneBy([
                'player' => $player,
                'card' => $data->getCard()
            ]);
            $player->removeCard($playerCard);
            $this->entityManager->remove($playerCard);

            $this->entityManager->persist($data);
            $this->entityManager->flush();

            $this->logger->notice('Card played (game: '.$data->getRound()->getGame()->getId().', round: '.$data->getRound()->getId().', player: ' . $data->getPlayer()->getName() . ', card: ' . $data->getCard()->getId() . ')');
        } catch(\Exception $e){
            throw new BadRequestHttpException($e->getMessage());
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