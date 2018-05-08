<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\Nullable;

final class NullableCharacter extends Nullable implements Character
{
    protected static function nonNullImplementation(): string
    {
        return NonNullCharacter::class;
    }

    protected static function nullImplementation(): string
    {
        return NullCharacter::class;
    }
}
