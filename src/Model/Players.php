<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\Set;

final class Players implements Set
{
    public function current(): Player
    {

    }

    public function byId(PlayerId $id): Player
    {
    }

    public function byCharacter(Character $character): Player
    {
    }

    public function withTurnAdvanced(): Players
    {
    }

    public function withPlayer(Player $player): Players
    {
    }
}
