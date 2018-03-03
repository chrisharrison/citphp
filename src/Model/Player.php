<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\ValueObject;

final class Player implements ValueObject
{
    public function round(): Round
    {
    }

    public function city(): Districts
    {
    }

    public function hand(): Districts
    {
    }

    public function purse(): GoldValue
    {
    }
}
