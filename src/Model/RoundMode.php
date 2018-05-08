<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\Enums\EnumTrait;
use Funeralzone\ValueObjects\ValueObject;

/**
 * @method static RoundMode CHOOSE_CHARACTER()
 * @method static RoundMode NORMAL()
 * @method static RoundMode GRAVEYARD()
 */
final class RoundMode implements ValueObject
{
    use EnumTrait;

    public const CHOOSE_CHARACTER = 0;
    public const NORMAL = 1;
    public const GRAVEYARD = 2;
}