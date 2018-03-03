<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\ValueObject;

final class District implements ValueObject
{
    public static function observatory(): self
    {
    }

    public static function library(): self
    {
    }

    public static function quarry(): self
    {
    }
}
