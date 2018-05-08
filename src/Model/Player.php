<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\ValueObject;

final class Player implements ValueObject
{
    public function id(): PlayerId
    {
    }

    public function currentCharacter(): Character
    {
    }

    public function isMurdered(): bool
    {
    }

    public function isVictimOfTheft(): bool
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

    public function withPurse(GoldValue $purse): Player
    {
    }

    public function withHand(Districts $hand): Player
    {
    }

    public function withCity(Districts $city): Player
    {
    }

    public function withCurrentCharacter(Character $character): Player
    {
    }

    public function withIsMurdered(): Player
    {
    }

    public function withIsVictimOfTheft(): Player
    {
    }
}
