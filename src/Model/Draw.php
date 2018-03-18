<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

interface Draw
{
    public function deckNow(): Deck;
    public function drawn(): Deck;
}
