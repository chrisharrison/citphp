<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\ValueObject;

final class Round implements ValueObject
{
    public function isTurn(): bool
    {
    }

    public function hasChosenCharacter(): bool
    {
    }

    public function isGraveyardTurn(): bool
    {
    }

    public function isDefaultActionInitiated(): bool
    {
    }

    public function isDefaultActionCompleted(): bool
    {
    }

    public function isSpecialPowerPlayed(): bool
    {
    }

    public function isDestroyDistrictPlayed(): bool
    {
    }

    public function isLaboratoryPowerPlayed(): bool
    {
    }

    public function isSmithyPowerPlayed(): bool
    {
    }

    public function potentialHand(): Districts
    {
    }

    public function character(): Character
    {
    }

    public function numberOfDistrictsBuilt(): int
    {
    }
}
