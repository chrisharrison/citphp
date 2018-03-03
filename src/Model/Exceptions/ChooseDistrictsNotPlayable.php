<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Exceptions;

use ChrisHarrison\Citphp\Model\Districts;
use ChrisHarrison\Citphp\Model\Player;
use Exception;

final class ChooseDistrictsNotPlayable extends Exception
{
    use TurnExceptionTrait;

    protected static function turnName(): string
    {
        // TODO: Implement turnName() method.
    }

    public static function notInHand(Player $player, Districts $districts): self
    {
    }

    public static function tooManyDistrictsChosen(Player $player, int $amountChosen, int $maxAmount): self
    {
    }
}
