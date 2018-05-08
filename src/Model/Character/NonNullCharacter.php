<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Character;

use ChrisHarrison\Citphp\Model\Characters;

final class NonNullCharacter implements Character
{
    public static function architect(): self
    {
    }

    public static function assassin(): self
    {
    }

    public static function thief(): self
    {
    }

    public static function magician(): self
    {
    }

    public static function king(): self
    {
    }

    public static function bishop(): self
    {
    }

    public static function merchant(): self
    {
    }

    public static function warlord(): self
    {
    }

    public function isOneOf(Characters $characters): bool
    {
    }
}
