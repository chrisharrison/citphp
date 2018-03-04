<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\Set;

final class Districts implements Set
{
    public function has(District $district): bool
    {
    }

    public function hasAll(Districts $districts): bool
    {
    }

    public function totalValue(): GoldValue
    {
    }

    public function numberOf(District $district): int
    {
    }

    public function size(): int
    {
    }
}
