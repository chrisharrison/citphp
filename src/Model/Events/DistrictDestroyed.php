<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Events;

use ChrisHarrison\Citphp\Model\District;
use ChrisHarrison\Citphp\Model\PlayerId;
use Prooph\EventSourcing\AggregateChanged;

final class DistrictDestroyed extends AggregateChanged
{
    public function playerId(): PlayerId
    {
    }

    public function victimId(): PlayerId
    {
    }

    public function district(): District
    {
    }
}
