<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Exceptions;

use ChrisHarrison\Citphp\Model\Player;
use Exception;

final class UseSmithyPowerNotPlayable extends Exception
{
    use TurnExceptionTrait;

    public static function haveNotBuiltSmithy(Player $player): self
    {
    }

    public static function cannotAfford(Player $player): self
    {
    }

    public static function smithyPowerPlayed(Player $player): self
    {
    }

    protected static function turnName(): string
    {
        // TODO: Implement turnName() method.
    }
}
