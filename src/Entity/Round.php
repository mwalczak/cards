<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\DTO\RoundCardDTO;
use App\Enum\RoundStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *  @ApiResource(
 *     collectionOperations={
 *         "post"
 *     },
 *     itemOperations={
 *         "get",
 *         "put"={"denormalization_context"={"groups"={"rounds:update"}}},
 *         "delete"
 *     },
 *     normalizationContext={"groups"={"rounds:read"}},
 *     denormalizationContext={"groups"={"rounds:write"}}
 * )
 * @ORM\EntityListeners(
 *     {
 *          "App\EventListener\RoundUpdateListener"
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\RoundRepository")
 */
class Round
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="rounds")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"rounds:write"})
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\QuestionCard")
     * @Groups({"rounds:read", "games:read"})
     */
    private $questionCard;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player")
     * @Groups({"rounds:update", "rounds:read", "games:read"})
     */
    private $winner;

    /**
     * @var RoundCard[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\RoundCard", mappedBy="round", orphanRemoval=true)
     * @Groups({"rounds:read", "games:read"})
     */
    private $answerCards;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"rounds:update", "rounds:read", "games:read"})
     */
    private $status;

    public function __construct()
    {
        $this->answerCards = new ArrayCollection();
        $this->status = RoundStatus::NEW();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getQuestionCard(): ?QuestionCard
    {
        return $this->questionCard;
    }

    public function setQuestionCard(?QuestionCard $questionCard): self
    {
        $this->questionCard = $questionCard;

        return $this;
    }

    public function getWinner(): ?Player
    {
        return $this->winner;
    }

    public function setWinner(?Player $winner): self
    {
        $this->winner = $winner;

        return $this;
    }

    /**
     * @return Collection|RoundCard[]
     */
    public function getAnswerCards(): Collection
    {
        return $this->answerCards;
    }

    public function addAnswerCard(RoundCard $answerCard): self
    {
        if (!$this->answerCards->contains($answerCard)) {
            $this->answerCards[] = $answerCard;
            $answerCard->setRound($this);
        }

        return $this;
    }

    public function removeAnswerCard(RoundCard $answerCard): self
    {
        if ($this->answerCards->contains($answerCard)) {
            $this->answerCards->removeElement($answerCard);
            // set the owning side to null (unless already changed)
            if ($answerCard->getRound() === $this) {
                $answerCard->setRound(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     * @Groups({"rounds:read", "games:read"})
     */
    public function getCardsPlayedCount(): int
    {
        return count($this->getAnswerCards());
    }

    /**
     * @return array
     *
     * @Groups({"rounds:read", "games:read"})
     */
    public function getPlayersAnswers(): array
    {
        /** @var RoundCardDTO[] $answers */
        $answers = [];

        foreach ($this->answerCards as $card){
            $player = $card->getPlayer();
            if(empty($answers[$player->getId()])){
                $answers[$player->getId()] = new RoundCardDTO($player, $card->getCard());
            } else {
                $answers[$player->getId()]->cards[] = $card->getCard();
            }
        }

        return $answers;
    }
}
