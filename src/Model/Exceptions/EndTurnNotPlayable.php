<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Exceptions;

use Exception;

final class EndTurnNotPlayable extends Exception
{
    use TurnExceptionTrait;

    protected static function turnName(): string
    {
        // TODO: Implement turnName() method.
    }
}
