<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Exceptions;

use ChrisHarrison\Citphp\Model\Character;
use ChrisHarrison\Citphp\Model\Player;
use Exception;

final class ChooseCharacterNotPlayable extends Exception
{
    use TurnExceptionTrait;

    public static function characterHasBeenDrawn(Player $attemptedPlayer, Character $attemptedCharacter): self
    {
    }

    protected static function turnName(): string
    {
        // TODO: Implement turnName() method.
    }
}
