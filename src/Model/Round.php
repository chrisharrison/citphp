<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\ValueObject;

final class Round implements ValueObject
{
    public function playerId(): PlayerId
    {
    }

    public function mode(): RoundMode
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

    public function numberOfDistrictsBuilt(): int
    {
    }

    public function withDefaultActionInitiated(): Round
    {
    }

    public function withDefaultActionCompleted(): Round
    {
    }

    public function withSpecialPowerPlayed(): Round
    {
    }

    public function withDestroyDistrictPlayed(): Round
    {
    }

    public function withLaboratoryPowerPlayed(): Round
    {
    }

    public function withSmithyPowerPlayed(): Round
    {
    }

    public function withPotentialHand(Districts $hand): Round
    {
    }

    public function withIncrementedNumberOfDistrictsBuilt(int $amount): Round
    {
    }
}
