<?php


namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static RoundStatus NEW()
 * @method static RoundStatus FINISHED()
 */
class RoundStatus extends Enum
{
    private const NEW = 'new';
    private const FINISHED = 'finished';
}