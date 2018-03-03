<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model\Exceptions;

use ChrisHarrison\Citphp\Model\Player;

trait TurnExceptionTrait
{
    /**
     * @return string
     */
    abstract protected static function turnName(): string;

    /**
     * @param Player $attemptedPlayer
     * @param Player $actualPlayer
     * @return static
     */
    public static function notPlayersTurn(Player $attemptedPlayer, Player $actualPlayer)
    {
    }

    /**
     * @param Player $player
     * @return static
     */
    public static function graveyardTurn(Player $player)
    {
    }

    /**
     * @param Player $player
     * @return static
     */
    public static function characterNotChosenYet(Player $player)
    {
    }

    /**
     * @param Player $player
     * @return static
     */
    public static function alreadyPlayed(Player $player)
    {
    }

    /**
     * @param Player $player
     * @return static
     */
    public static function defaultActionInitiated(Player $player)
    {
    }

    /**
     * @param Player $player
     * @return static
     */
    public static function defaultActionCompleted(Player $player)
    {
    }

    /**
     * @param Player $player
     * @return static
     */
    public static function defaultActionNotCompleted(Player $player)
    {
    }
}
