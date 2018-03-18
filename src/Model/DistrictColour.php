<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\Enums\EnumTrait;
use Funeralzone\ValueObjects\ValueObject;

/**
 * @method static DistrictColour RED()
 * @method static DistrictColour GREEN()
 * @method static DistrictColour BLUE()
 * @method static DistrictColour YELLOW()
 */
final class DistrictColour implements ValueObject
{
    use EnumTrait;

    public const RED = 0;
    public const GREEN = 1;
    public const BLUE = 2;
    public const YELLOW = 3;
}
