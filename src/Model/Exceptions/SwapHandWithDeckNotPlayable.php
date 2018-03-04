<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Exceptions;

use ChrisHarrison\Citphp\Model\Districts;
use ChrisHarrison\Citphp\Model\Player;
use Exception;

final class SwapHandWithDeckNotPlayable extends Exception
{
    use TurnExceptionTrait;

    public static function notTheMagician(Player $player): self
    {
    }

    public static function mustSwapAtLeastOne(Player $player): self
    {
    }

    public static function districtsMustBeInHand(Player $player, Districts $districts): self
    {
    }

    protected static function turnName(): string
    {
        // TODO: Implement turnName() method.
    }
}