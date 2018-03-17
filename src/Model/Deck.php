<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\Set;

interface Deck extends Set
{
    public function draw(int $amount): Deck;
}