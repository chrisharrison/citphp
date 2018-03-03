<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Exceptions;

use ChrisHarrison\Citphp\Model\District;
use ChrisHarrison\Citphp\Model\Districts;
use ChrisHarrison\Citphp\Model\GoldValue;
use ChrisHarrison\Citphp\Model\Player;
use Exception;

final class BuildDistrictsNotPlayable extends Exception
{
    use TurnExceptionTrait;

    protected static function turnName(): string
    {
        // TODO: Implement turnName() method.
    }

    public static function willExceedMaximumDistricts(Player $player, int $maximumDistrictsThisRound): self
    {
    }

    public static function notInHand(Player $player, Districts $hand): self
    {
    }

    public static function cannotAfford(Player $player, GoldValue $total): self
    {
    }

    public static function cityIsFull(Player $player, District $district, int $maximumDuplicates): self
    {
    }
}
