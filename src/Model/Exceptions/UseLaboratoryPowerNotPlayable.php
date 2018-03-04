<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Exceptions;

use ChrisHarrison\Citphp\Model\District;
use ChrisHarrison\Citphp\Model\Player;
use Exception;

final class UseLaboratoryPowerNotPlayable extends Exception
{
    use TurnExceptionTrait;

    public static function haveNotBuiltLaboratory(Player $player): self
    {
    }

    public static function districtNotInHand(Player $player, District $district): self
    {
    }

    public static function laboratoryPowerPlayed(Player $player): self
    {
    }

    protected static function turnName(): string
    {
        // TODO: Implement turnName() method.
    }
}
