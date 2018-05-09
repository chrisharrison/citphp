<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjectExtensions\ComplexScalars\UUIDTrait;
use Funeralzone\ValueObjects\ValueObject;

final class GameId implements ValueObject
{
    use UUIDTrait;
}
