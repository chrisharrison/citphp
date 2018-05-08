<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\Set;

final class Players implements Set
{
    public function byId(PlayerId $id): Player
    {
    }

    public function byCharacter(Character $character): Player
    {
    }

    public function withPlayer(Player $player): Players
    {
    }
}
