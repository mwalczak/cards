<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\DTO\ScoreDTO;
use App\Enum\RoundStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *  @ApiResource(
 *     collectionOperations={
 *         "post"
 *     },
 *     itemOperations={
 *         "get",
 *         "put"
 *     },
 *     normalizationContext={"groups"={"games:read"}},
 *     denormalizationContext={"groups"={"games:write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Game
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="App\Generator\UniqIdGenerator")
     * @ORM\Column(type="string", length=13, unique=true, options={"fixed" = true})
     * @Groups({"games:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Player", mappedBy="game")
     * @Groups({"games:read"})
     */
    private $players;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Round", mappedBy="game")
     * @Groups({"games:read"})
     */
    private $rounds;

    /**
     * @Groups({"games:write"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=15)
     * @Groups({"games:write", "games:read"})
     * @Assert\NotBlank()
     */
    private $type;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->rounds = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTime();

        return $this;
    }

    /**
     * @return Collection|Player[]
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
            $player->setGame($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->contains($player)) {
            $this->players->removeElement($player);
            // set the owning side to null (unless already changed)
            if ($player->getGame() === $this) {
                $player->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Round[]
     */
    public function getRounds(): Collection
    {
        return $this->rounds;
    }

    public function addRound(Round $round): self
    {
        if (!$this->rounds->contains($round)) {
            $this->rounds[] = $round;
            $round->setGame($this);
        }

        return $this;
    }

    public function removeRound(Round $round): self
    {
        if ($this->rounds->contains($round)) {
            $this->rounds->removeElement($round);
            // set the owning side to null (unless already changed)
            if ($round->getGame() === $this) {
                $round->setGame(null);
            }
        }

        return $this;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return Round|null
     * @Groups({"games:read"})
     */
    public function getActiveRound(): ?Round
    {
        foreach($this->getRounds() as $round){
            if($round->getStatus() == RoundStatus::NEW()){
                return $round;
            }
        }

        return null;
    }

    /**
     * @return int
     * @Groups({"games:read"})
     */
    public function getRoundsCount(): int
    {
        return $this->getRounds()->count();
    }

    /**
     * @return array
     * @Groups({"games:read"})
     */
    public function getScores(): array
    {
        $scores = [];
        foreach($this->getRounds() as $round){
            if(!$round->getWinner()){
                continue;
            }
            if(!isset($scores[$round->getWinner()->getName()])){
                $score = new ScoreDTO($round->getWinner()->getName(), 1);
                $scores[$round->getWinner()->getName()] = $score;
            } else {
                $scores[$round->getWinner()->getName()]->score++;
            }
        }
        krsort($scores);
        foreach($this->getPlayers() as $player){
            if(!isset($scores[$player->getName()])){
                $score = new ScoreDTO($player->getName(), 0);
                $scores[$player->getName()] = $score;
            }
        }

        return array_values($scores);
    }

    /**
     * @return int[]
     * @Groups({"games:read"})
     */
    public function getUsedQuestions(): array
    {
        $usedQuestions = [];
        foreach($this->getRounds() as $round){
            if(!$round->getQuestionCard()){
                continue;
            }
            $usedQuestions[] = $round->getQuestionCard()->getId();
        }

        return $usedQuestions;
    }

    public function getUsedAnswers(): array
    {
        $usedAnswers = [];
        foreach($this->getRounds() as $round){
            foreach($round->getAnswerCards() as $roundCard){
                $usedAnswers[] = $roundCard->getCard()->getId();
            }
        }
        return $usedAnswers;
    }

    public function getPlayersCards(): array
    {
        $playersCards = [];
        foreach($this->getPlayers() as $player){
            foreach($player->getCards() as $playerCard){
                $playersCards[] = $playerCard->getCard()->getId();
            }
        }
        return $playersCards;
    }

    /**
     * @return int
     * @Groups({"games:read"})
     */
    public function getUsedAnswersCount(): int
    {
        return count($this->getUsedAnswers());
    }


    /**
     * @return int
     * @Groups({"games:read"})
     */
    public function getPlayersCount(): int
    {
        return count($this->getPlayers());
    }

    /**
     * @return string[]
     */
    public function getPlayersNames(): array
    {
        $names = [];
        foreach($this->getPlayers() as $player){
            $names[] = $player->getName();
        }

        return $names;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }


}
