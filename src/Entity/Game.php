<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Symfony\Component\Serializer\Annotation\Groups;

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
     */
    private $players;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Round", mappedBy="game")
     * @Groups({"games:read"})
     */
    private $rounds;

    public function __construct()
    {
        $this->players = new ArrayCollection();
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
                $scores[$round->getWinner()->getName()] = 1;
            } else {
                $scores[$round->getWinner()->getName()]++;
            }
        }
        krsort($scores);
        return $scores;
    }
}
