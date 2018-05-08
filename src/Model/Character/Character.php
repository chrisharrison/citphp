<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Character;

use ChrisHarrison\Citphp\Model\Characters;
use Funeralzone\ValueObjects\ValueObject;

interface Character extends ValueObject
{
    public function isOneOf(Characters $characters): bool;
}
