<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "post"
 *     },
 *     itemOperations={},
 *     normalizationContext={"groups"={"round_cards:read"}},
 *     denormalizationContext={"groups"={"round_cards:write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\RoundCardRepository")
 */
class RoundCard
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Round", inversedBy="answerCards")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"round_cards:write", "round_cards:read"})
     */
    private $round;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AnswerCard")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"round_cards:write", "round_cards:read"})
     */
    private $card;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"round_cards:write", "round_cards:read"})
     */
    private $player;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRound(): ?Round
    {
        return $this->round;
    }

    public function setRound(?Round $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function getCard(): ?AnswerCard
    {
        return $this->card;
    }

    public function setCard(?AnswerCard $card): self
    {
        $this->card = $card;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }
}
