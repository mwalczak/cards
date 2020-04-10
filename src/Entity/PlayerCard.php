<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerCardRepository")
 */
class PlayerCard
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="cards")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AnswerCard")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"players:read"})
     */
    private $card;

    /**
     * PlayerCard constructor.
     * @param $player
     * @param $card
     */
    public function __construct(Player $player, AnswerCard $card)
    {
        $this->player = $player;
        $this->card = $card;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCard(): ?AnswerCard
    {
        return $this->card;
    }

    public function setCard(?AnswerCard $card): self
    {
        $this->card = $card;

        return $this;
    }
}
