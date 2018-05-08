<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\ValueObject;

interface Character extends ValueObject
{
    public function isOneOf(Characters $characters): bool;
}
