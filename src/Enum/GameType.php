<?php


namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static GameType CARDS()
 * @method static GameType MEMES()
 */
class GameType extends Enum
{
    private const CARDS = 'cards';
    private const MEMES = 'memes';
}