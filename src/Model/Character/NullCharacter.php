<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Character;

use Funeralzone\ValueObjects\NullTrait;

final class NullCharacter implements Character
{
    use NullTrait;
}
