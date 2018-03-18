<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\ValueObjects\ValueObject;

interface Deck extends ValueObject
{
    /**
     * @param ValueObject $value
     * @return bool
     */
    public function has(ValueObject $value): bool;

    /**
     * @param Deck $deck
     * @return bool
     */
    public function hasAll(Deck $deck): bool;

    /**
     * @param ValueObject $value
     * @return int
     */
    public function numberOf(ValueObject $value): int;

    /**
     * @return int
     */
    public function count(): int;

    /**
     * @param Deck $deck
     * @return static
     */
    public function putOnTop(Deck $deck);

    /**
     * @param Deck $deck
     * @return static
     */
    public function putOnBottom(Deck $deck);

    /**
     * @param Deck $deck
     * @return static
     */
    public function remove(Deck $deck);

    /**
     * @param int $amountToDraw
     * @return Draw
     */
    public function draw(int $amountToDraw): Draw;
}