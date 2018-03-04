<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Exceptions;

use ChrisHarrison\Citphp\Model\District;
use ChrisHarrison\Citphp\Model\Player;
use Exception;

final class DestroyDistrictNotPlayable extends Exception
{
    use TurnExceptionTrait;

    public static function notTheWarlord(Player $player): self
    {
    }

    public static function destroyDistrictPlayed(Player $player): self
    {
    }

    public static function victimHasNotBuiltDistrict(Player $player): self
    {
    }

    public static function cannotAfford(Player $player, District $district): self
    {
    }

    public static function completeCity(Player $player, Player $victim): self
    {
    }

    public static function cannotDestroyKeep(Player $player): self
    {
    }

    protected static function turnName(): string
    {
        // TODO: Implement turnName() method.
    }
}
