<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Events;

use ChrisHarrison\Citphp\Model\PlayerId;
use Prooph\EventSourcing\AggregateChanged;

final class UsedSmithyPower extends AggregateChanged
{
    public function playerId(): PlayerId
    {
    }
}
