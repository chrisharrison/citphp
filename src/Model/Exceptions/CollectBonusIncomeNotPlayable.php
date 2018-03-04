<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Exceptions;

use ChrisHarrison\Citphp\Model\Player;
use Exception;

final class CollectBonusIncomeNotPlayable extends Exception
{
    use TurnExceptionTrait;

    public static function notPlayingABonusIncomeCharacter(Player $player): self
    {
    }

    protected static function turnName(): string
    {
        // TODO: Implement turnName() method.
    }
}
