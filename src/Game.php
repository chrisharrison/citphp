<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp;

final class Game extends AggregateRoot
{
	private $players;
	private $characterDeck;
	private $districtDeck;

	public function chooseCharacter(PlayerId $player, Character $character)
	{
		// Check if it's this player's turn

		// Check the player has not already chosen a character

		// Check if the card has not already been chosen

		// EVENT EMITTED: CharacterChosen
	}

	public function takeGold()
	{
		// Check this is not a graveyard turn

		// Check they have chosen a character

		// Check if it's the player's turn

		// Check they haven't already initiated their action

		// EVENT EMITTED: GoldTaken
	}

	public function drawDistricts()
	{
		// Check this is not a graveyard turn

		// Check they have chosen a character

		// Check if it's the player's turn

		// Check they haven't already initiated their action

		// If the player has built the Observatory district, they can draw 3 cards, else 2

		// EVENT EMITTED: DistrictsDrawn
	}

	public function chooseDistricts()
	{
		// Check this is not a graveyard turn

		// Check they have chosen a character

		// Check if it's the player's turn

		// Check if the districts they want to choose were drawn

		// Check they haven't already completed their action

		// If the player has built the Library district, they can choose 2 cards, else 1

		// EVENT EMITTED: DistrictsChosen
	}

	public function buildDistricts()
	{
		// Check this is not a graveyard turn

		// Check they have completed their action

		// Check if it's the player's turn

		// Check they haven't already built their maximum districts this round (if playing architect it's 3, else 1)

		// Check they have the district in their hand

		// Check they can afford to build the district

		// Check they don't already have the district in their city. If they have built the Quarry district they can build 1 duplicate.

		// EVENT EMITTED: DistrictsBuilt
	}

	public function murder()
	{
		// Check this is not a graveyard turn

		// Check it's the player's turn

		// Check they are playing the assasin

		// Check they haven't already exercised their power

		// EVENT EMITTED: Murdered
	}

	public function steal()
	{
		// Check this is not a graveyard turn

		// Check it's the player's turn

		// Check they are playing the thief

		// Check they haven't already exercised their power

		// EVENT EMITTED: Theft
	}

	public function swapHandWithPlayer()
	{
		// Check this is not a graveyard turn

		// Check it's the player's turn

		// Check they are playing the magician

		// Check they haven't already exercised their power

		// EVENT EMITTED: SwappedHandWithPlayer
	}

	public function swapHandWithDeck()
	{
		// Check this is not a graveyard turn

		// Check it's the player's turn

		// Check they are playing the magician

		// Check they want to swap at least 1 card

		// CHeck they have enough cards in their hand

		// Check they haven't already exercised their power

		// EVENT EMITTED: SwappedHandWithDeck
	}

	public function collectBonusIncome()
	{
		// Check this is not a graveyard turn

		// Check it's the player's turn

		// Check they are playing the king, bishop, merchant or warlord

		// Check they haven't already exercised their power

		// EVENT EMITTED: CollectedBonusIncome
	}

	public function destroy()
	{
		// Check this is not a graveyard turn

		// Check it's the player's turn

		// Check they are playing the warlord

		// Check they have completed their action

		// Check they haven't already destroyed

		// Check they can afford to destroy the district (taking into account that if the victim has built the Great Wall, the prices are higher)

		// Check the victim has the district in their city

		// Check the victim's city is not complete [8 districts, 7 if bell tower]

		// Check they are not trying to destroy a 'Keep' as it's indestructible

		// EVENT EMITTED: Destroyed
	}

	public function endTurn()
	{
		// Check it's the player's turn

		// Check they have completed their action

		// EVENT EMITTED: TurnEnded
	}

	public function useLaboratoryPower()
	{
		// Check it's the player's turn

		// Check they have built the Labaratory

		// Check they have the district they want to discard in their hand

		// Check they have not already used this power

		// EVENT EMITTED: UsedLaboratoryPower
	}

	public function useSmithyPower()
	{
		// Check it's the player's turn

		// Check they have built the Smithy

		// Check they can afford to use the power (2 gold)

		// Check they have not already used this power

		// EVENT EMITTED: UsedSmithyPower
	}

	public function useGraveyardPower()
	{
		// Check if this is a graveyard turn

		// Check it's the player's turn

		// Check they can afford to use the power (1 gold)

		// Check the district they are trying to use this power on is not the graveyard itself

		// Check they have not already used this power

		// EVENT EMITTED: UsedGraveyardPower
	}
}
