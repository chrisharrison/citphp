<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\ValueObject;

final class Round implements ValueObject
{
    private $character;
    private $isTurn;
    private $isGraveyardTurn;
    private $isDefaultActionInitiated;
    private $isDefaultActionCompleted;
    private $isSpecialPowerPlayed;
    private $isDestroyDistrictPlayed;
    private $isLaboratoryPowerPlayed;
    private $isSmithyPowerPlayed;

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

    public function withCharacter(Character $character): Round
    {
    }

    public function withDefaultActionCompleted(): Round
    {
    }

    public function withDefaultActionInitiated(): Round
    {
    }

    public function withSpecialPowerPlayed(): Round
    {
    }

    public function withPotentialHand(Districts $hand): Round
    {
    }

    public function withIncrementedNumberOfDistrictsBuilt(int $amount): Round
    {
    }

    public function murdered(): Round
    {
    }

    public function victimOfTheft(): Round
    {
    }
}
