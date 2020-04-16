<?php


namespace App\DTO;


use JsonSerializable;

class ScoreDTO implements JsonSerializable
{
    public string $player;
    public int $score;

    /**
     * ScoreDTO constructor.
     * @param string $player
     * @param int $score
     */
    public function __construct(?string $player, int $score)
    {
        $this->player = (string)$player;
        $this->score = $score;
    }

    public function jsonSerialize()
    {
        return
            [
                'player' => $this->player,
                'score' => $this->score
            ];
    }
}