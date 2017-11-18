<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp;

final class Game extends AggregateRoot
{
	private $players;
	private $characterDeck;
	private $districtDeck;

	public function chooseCharacter(PlayerId $playerId, Character $character)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw ChooseCharacterNotPlayable::notPlayersTurn($player, $this->players->current())
		}

		// Check the player has not already chosen a character
		if ($player->round()->hasChosenCharacter()) {
			throw ChooseCharacterNotPlayable::alreadyPlayed($player);
		}

		// Check if the character has not already been chosen
		if (!$this->characterDeck->in($character)) {
			throw ChooseCharacterNotPlayable::characterHasBeenDrawn($player, $character);
		}

		// EVENT EMITTED: CharacterChosen
	}

	public function takeGold(PlayerId $player)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw TakeGoldNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check this is not a graveyard turn
		if ($player->round()->isGraveyardTurn()) {
			throw TakeGoldNotPlayable::graveyardTurn($player);
		}

		// Check they have chosen a character
		if ($player->round()->hasChosenCharacter()) {
			throw TakeGoldNotPlayable::characterNotChosenYet($player);
		}

		// Check they haven't already initiated their default action
		if ($player->round()->defaultActionInitiated()) {
			throw TakeGoldNotPlayable::alreadyPlayed($player);
		}

		// EVENT EMITTED: GoldTaken
	}

	public function drawDistricts(PlayerId $player)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw DrawDistrictsNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check this is not a graveyard turn
		if ($player->round()->isGraveyardTurn()) {
			throw DrawDistrictsNotPlayable::graveyardTurn($player);
		}

		// Check they have chosen a character
		if ($player->round()->hasChosenCharacter()) {
			throw DrawDistrictsNotPlayable::characterNotChosenYet($player);
		}

		// Check they haven't already initiated their default action
		if ($player->round()->defaultActionInitiated()) {
			throw DrawDistrictsNotPlayable::defaultActionInitiated($player);
		}

		// If the player has built the Observatory district, they can draw 3 cards, else 2

		// EVENT EMITTED: DistrictsDrawn
	}

	public function chooseDistricts(PlayerId $player, Districts $districts)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw ChooseDistrictsNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check this is not a graveyard turn
		if ($player->round()->isGraveyardTurn()) {
			throw ChooseDistrictsNotPlayable::graveyardTurn($player);
		}

		// Check if the districts they want to choose were drawn
		if (!$player->round()->potentialHand()->in($districts)) {
			throw ChooseDistrictsNotPlayable::notInHand($player, $districts);
		}

		// Check they haven't already completed their default action
		if ($player->round()->defaultActionCompleted()) {
			throw ChooseDistrictsNotPlayable::defaultActionCompleted($player);
		}

		// If the player has built the Library district, they can choose 2 cards, else 1
		//TODO

		// EVENT EMITTED: DistrictsChosen
	}

	public function buildDistricts(PlayerId $player, Districts $districts)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw BuildDistrictsNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check this is not a graveyard turn
		if ($player->round()->isGraveyardTurn()) {
			throw BuildDistrictsNotPlayable::graveyardTurn($player);
		}

		// Check they have completed their default action
		if (!$player->round()->defaultActionCompleted()) {
			throw BuildDistrictsNotPlayable::defaultActionNotCompleted($player);
		}

		// Check they haven't already built their maximum districts this round (if playing architect it's 3, else 1)
		if ($player->round()->character()->sameValueAs(Character::architect())) {
			$maximumDistrictsThisRound = 3;
		} else {
			$maximumDistrictsThisRound = 1;
		}

		if ($player->round()->numberOfDistrictsBuilt() + count($districts) > $maximumDistrictsThisRound) {
			throw BuildDistrictsNotPlayable::exceedMaximumDistricts($player, $maximumDistrictsThisRound);
		}

		// Check they have the districts in their hand
		if (!$player->hand()->in($districts)) {
			throw BuildDistrictsNotPlayable::notInHand($player, $districts);
		}

		// Check they can afford to build the districts
		if ($districts->totalValue() > $player->gold()) {
			throw BuildDistrictsNotPlayable::cannotAfford($player, $districts->totalValue, $player->gold());
		}

		// Check they don't already have the districts in their city. If they have built the Quarry district they can build 1 duplicate.

		if ($player->city()->has(District::quarry())) {
			$maximumDuplicates = 1;
		} else {
			$maximumDuplicates = 0;
		}

		foreach ($districts as $district) {
			if ($player->city()->total($district) > $maximumDuplicates) {
				throw BuildDistrictsNotPlayable::cityIsFull($player, $district, $maximumDuplicates);
			}
		}

		// EVENT EMITTED: DistrictsBuilt
	}

	public function murder(PlayerId $player, Character $victim)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw MurderNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check this is not a graveyard turn
		if ($player->round()->isGraveyardTurn()) {
			throw MurderNotPlayable::graveyardTurn($player);
		}

		// Check they are playing the assassin
		if (!$player->round()->character()->sameValueAs(Character::assassin())) {
			throw MurderNotPlayable::notTheAssassin($player);
		}

		// Check they haven't already exercised their power
		if ($player->round()->playedSpecialPower()) {
			throw MurderNotPlayable::playedSpecialPower($player);
		}

		// EVENT EMITTED: Murdered
	}

	public function steal(PlayerId $player, Character $victim)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw StealNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check this is not a graveyard turn
		if ($player->round()->isGraveyardTurn()) {
			throw StealNotPlayable::graveyardTurn($player);
		}

		// Check they are playing the thief
		if (!$player->round()->character()->sameValueAs(Character::thief())) {
			throw StealNotPlayable::notTheThief($player);
		}

		// Check they haven't already exercised their power
		if ($player->round()->playedSpecialPower()) {
			throw StealNotPlayable::playedSpecialPower($player);
		}

		// EVENT EMITTED: Theft
	}

	public function swapHandWithPlayer(PlayerId $player, PlayerId $victim)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw SwapHandWithPlayerNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check this is not a graveyard turn
		if ($player->round()->isGraveyardTurn()) {
			throw SwapHandWithPlayerNotPlayable::graveyardTurn($player);
		}

		// Check they are playing the magician
		if (!$player->round()->character()->sameValueAs(Character::magician())) {
			throw SwapHandWithPlayerNotPlayable::notTheMagician($player);
		}

		// Check they haven't already exercised their power
		if ($player->round()->playedSpecialPower()) {
			throw SwapHandWithPlayerNotPlayable::playedSpecialPower($player);
		}

		// EVENT EMITTED: SwappedHandWithPlayer
	}

	public function swapHandWithDeck(PlayerId $player, Districts $districts)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw SwapHandWithDeckNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check this is not a graveyard turn
		if ($player->round()->isGraveyardTurn()) {
			throw SwapHandWithDeckNotPlayable::graveyardTurn($player);
		}

		// Check they are playing the magician
		if (!$player->round()->character()->sameValueAs(Character::magician())) {
			throw SwapHandWithDeckNotPlayable::notTheMagician($player);
		}

		// Check they haven't already exercised their power
		if ($player->round()->playedSpecialPower()) {
			throw SwapHandWithDeckNotPlayable::playedSpecialPower($player);
		}

		// Check they want to swap at least 1 card
		if (count($districts) == 0) {
			throw SwapHandWithDeckNotPlayable::mustSwapAtLeastOne($player);
		}

		// Check they have the districts they want to swap in their hand
		if (!$player->hand()->has($districts)) {
			throw SwapHandWithDeckNotPlayable::districtsMustBeInHand($player, $districts);
		}

		// EVENT EMITTED: SwappedHandWithDeck
	}

	public function collectBonusIncome(PlayerId $player)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw CollectBonusIncomeNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check this is not a graveyard turn
		if ($player->round()->isGraveyardTurn()) {
			throw CollectBonusIncomeNotPlayable::graveyardTurn($player);
		}

		// Check they are playing the king, bishop, merchant or warlord
		if (!$player->round()->character()->isOneOf(new Characters([
			Character::king(),
			Character::bishop(),
			Character::merchant(),
			Character::warlord(),
		]))) {
			throw CollectBonusIncomeNotPlayable::notPlayingABonusIncomeCharacter($player);
		}

		// Check they haven't already exercised their power
		if ($player->round()->playedSpecialPower()) {
			throw CollectBonusIncomeNotPlayable::playedSpecialPower($player);
		}

		// EVENT EMITTED: CollectedBonusIncome
	}

	public function destroyDistrict(PlayerId $player, PlayerId $victim, District $district)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw DestroyDistrictNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check this is not a graveyard turn
		if ($player->round()->isGraveyardTurn()) {
			throw DestroyDistrictNotPlayable::graveyardTurn($player);
		}

		// Check they are playing the warlord
		if (!$player->round()->character()->sameValueAs(Character::warlord())) {
			throw DestroyDistrictNotPlayable::notTheWarlord($player);
		}

		// Check they have completed their action
		if (!$player->round()->defaultActionCompleted()) {
			throw DestroyDistrictNotPlayable::defaultActionNotCompleted($player);
		}

		// Check they haven't already destroyed
		if ($player->round()->playedDestroy()) {
			throw DestroyDistrictNotPlayable::playedDestroy($player);
		}

		// Check they can afford to destroy the district (taking into account that if the victim has built the Great Wall, the prices are higher)

		// Check the victim has the district in their city

		// Check the victim's city is not complete [8 districts, 7 if bell tower]

		// Check they are not trying to destroy a 'Keep' as it's indestructible

		// EVENT EMITTED: DistrcitDestroyed
	}

	public function endTurn(PlayerId $player)
	{
		// Check it's the player's turn

		// Check they have completed their action

		// EVENT EMITTED: TurnEnded
	}

	public function useLaboratoryPower(PlayerId $player, District $district)
	{
		// Check it's the player's turn

		// Check they have built the Labaratory

		// Check they have the district they want to discard in their hand

		// Check they have not already used this power

		// EVENT EMITTED: UsedLaboratoryPower
	}

	public function useSmithyPower(PlayerId $player)
	{
		// Check it's the player's turn

		// Check they have built the Smithy

		// Check they can afford to use the power (2 gold)

		// Check they have not already used this power

		// EVENT EMITTED: UsedSmithyPower
	}

	public function useGraveyardPower(PlayerId $player)
	{
		// Check if this is a graveyard turn

		// Check it's the player's turn

		// Check they can afford to use the power (1 gold)

		// Check the district they are trying to use this power on is not the graveyard itself

		// Check they have not already used this power

		// EVENT EMITTED: UsedGraveyardPower
	}

	private function onCharacterChosen()
	{

	}

	private function onGoldTaken()
	{

	}

	private function onDistrcitsDrawn()
	{

	}

	private function onDistrictsChosen()
	{

	}

	private function onDistrictsBuilt()
	{

	}

	private function onMurdered()
	{

	}

	private function onTheft()
	{

	}

	private function onSwappedHandWithPlayer()
	{

	}

	private function onSwappedHandWithDeck()
	{

	}

	private function onCollectedBonusIncome()
	{

	}

	private function onDistrictDestroyed()
	{

	}

	private function onTurnEnded()
	{

	}

	private function onUsedLaboratoryPower()
	{

	}

	private function onUsedSmithyPower()
	{

	}

	private function onUsedGraveyardPower()
	{

	}
}
