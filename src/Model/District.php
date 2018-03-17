<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\ValueObject;

final class District implements ValueObject
{
    public function value(): GoldValue
    {
    }

    public static function observatory(): self
    {
    }

    public static function library(): self
    {
    }

    public static function quarry(): self
    {
    }

    public static function greatWall(): self
    {
    }

    public static function keep(): self
    {
    }

    public static function laboratory(): self
    {
    }

    public static function smithy(): self
    {
    }

    public static function bellTower(): self
    {
    }
}
