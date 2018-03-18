<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\Scalars\IntegerTrait;
use Funeralzone\ValueObjects\ValueObject;

final class GoldValue implements ValueObject
{
    use IntegerTrait;

    public function isMoreThan(GoldValue $value): bool
    {
    }

    public function isLessThan(GoldValue $value): bool
    {
    }

    public function withIncrement(GoldValue $value): GoldValue
    {
    }

    public function withDecrement(GoldValue $value): GoldValue
    {
    }
}
