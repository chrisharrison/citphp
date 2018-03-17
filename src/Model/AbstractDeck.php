<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

abstract class AbstractDeck implements Deck
{
    public function __construct(array $values)
    {
    }
    // TODO: add/remove methods need to take into account duplicate cards
}
