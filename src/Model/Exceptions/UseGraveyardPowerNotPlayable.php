<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Exceptions;

use ChrisHarrison\Citphp\Model\Player;
use Exception;

final class UseGraveyardPowerNotPlayable extends Exception
{
    use TurnExceptionTrait;

    public static function notAGraveyardTurn(Player $player): self
    {
    }

    public static function cannotAfford(Player $player): self
    {
    }

    protected static function turnName(): string
    {
        // TODO: Implement turnName() method.
    }
}
