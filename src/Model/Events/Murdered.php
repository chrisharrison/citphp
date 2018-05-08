<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Events;

use ChrisHarrison\Citphp\Model\Character\Character;
use ChrisHarrison\Citphp\Model\PlayerId;
use Prooph\EventSourcing\AggregateChanged;

final class Murdered extends AggregateChanged
{
    public function playerId(): PlayerId
    {
    }

    public function victim(): Character
    {
    }
}
