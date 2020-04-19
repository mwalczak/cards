<?php


namespace App\DTO;


use App\Entity\AnswerCard;
use App\Entity\Player;
use JsonSerializable;

class RoundCardDTO implements JsonSerializable
{
    public Player $player;
    public array $cards;

    public function __construct(Player $player, AnswerCard $card)
    {
        $this->player = $player;
        $this->cards[] = $card;
    }

    public function jsonSerialize()
    {
        return
            [
                'player' => $this->player,
                'cards' => $this->cards
            ];
    }
}