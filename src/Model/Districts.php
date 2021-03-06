<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

final class Districts extends AbstractDeck implements Deck
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

    public function byDistrictColour(DistrictColour $colour): Districts
    {
    }
}
