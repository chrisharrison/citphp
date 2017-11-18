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
		$maxNumberOfCardsToBeChosen = 1;
		if ($victim->city()->has(District::library())) {
			$maxNumberOfCardsToBeChosen = 2;
		}

		if (count($districts) > $maxNumberOfCardsToBeChosen) {
			throw ChooseDistrictsNotPlayable::tooManyDistrictsChosen($player, $districts);
		}

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
		if ($districts->totalValue() > $player->purse()) {
			throw BuildDistrictsNotPlayable::cannotAfford($player, $districts->totalValue(), $player->purse());
		}

		// Check they don't already have the districts in their city. If they have built the Quarry district they can build 1 duplicate.

		if ($player->city()->has(District::quarry())) {
			$maximumDuplicates = 1;
		} else {
			$maximumDuplicates = 0;
		}

		foreach ($districts as $district) {
			if ($player->city()->numberOf($district) > $maximumDuplicates) {
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

	public function destroyDistrict(PlayerId $player, PlayerId $victimId, District $district)
	{
		$player = $this->players->byId($playerId);
		$victim = $this->players->byId($victimId);

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

		// Check the victim has the district in their city
		if (!$victim->city()->has($district)) {
			throw DestroyDistrictNotPlayable::victimHasNotBuiltDistrict($player);
		}

		// Check they can afford to destroy the district...
		// ...(taking into account that if the victim has built the Great Wall, the prices are higher)
		$priceModifier = 0;

		if ($victim->city()->has(District::greatWall())) {
			$priceModifier = 1;
		}

		if ($player->purse() < $district->value() + $priceModifier) {
			throw DestroyDistrictNotPlayable::cannotAfford($player, $district, $player->purse());
		}

		// Check the victim's city is not complete [8 districts, 7 if bell tower]
		if ($victim->city()->size() >= $this->sizeOfCompletedCity()) {
			throw DestroyDistrictNotPlayable::completeCity($player, $victim);
		}

		// Check they are not trying to destroy a 'Keep' as it's indestructible
		if ($district->sameValueAs(District::keep())) {
			throw DestroyDistrictNotPlayable::cannotDestroyKeep($player);
		}

		// EVENT EMITTED: DistrcitDestroyed
	}

	public function endTurn(PlayerId $player)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw EndTurnNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check they have completed their default action
		if (!$player->round()->defaultActionCompleted()) {
			throw BuildDistrictsNotPlayable::defaultActionNotCompleted($player);
		}

		// EVENT EMITTED: TurnEnded
	}

	public function useLaboratoryPower(PlayerId $player, District $district)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw UseLaboratoryPowerNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check they have chosen a character
		if ($player->round()->hasChosenCharacter()) {
			throw UseLaboratoryPowerNotPlayable::characterNotChosenYet($player);
		}

		// Check this is not a graveyard turn
		if ($player->round()->isGraveyardTurn()) {
			throw UseLaboratoryPowerNotPlayable::graveyardTurn($player);
		}

		// Check they have built the Labaratory
		if (!$player->city()->has(District::laboratory())) {
			throw UseLaboratoryPowerNotPlayable::haveNotBuiltLaboratory($player);
		}

		// Check they have the district they want to discard in their hand
		if (!$player->hand()->has($district)) {
			throw UseLaboratoryPowerNotPlayable::districtNotInHand($player, $district);
		}

		// Check they have not already used this power
		if ($player->round()->playedLaboratoryPower()) {
			throw UseLaboratoryPowerNotPlayable::playedLaboratoryPower($player);
		}

		// EVENT EMITTED: UsedLaboratoryPower
	}

	public function useSmithyPower(PlayerId $player)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw UseSmithyPowerNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check they have chosen a character
		if ($player->round()->hasChosenCharacter()) {
			throw UseSmithyPowerNotPlayable::characterNotChosenYet($player);
		}

		// Check this is not a graveyard turn
		if ($player->round()->isGraveyardTurn()) {
			throw UseSmithyPowerNotPlayable::graveyardTurn($player);
		}

		// Check they have built the Smithy
		if (!$player->city()->has(District::smithy())) {
			throw UseSmithyPowerNotPlayable::haveNotBuiltSmithy($player);
		}

		// Check they can afford to use the power (2 gold)
		if ($player->purse() < 2) {
			throw UseSmithyPowerNotPlayable::cannotAfford($player, $player->purse());
		}

		// Check they have not already used this power
		if ($player->round()->playedSmithyPower()) {
			throw UseSmithyPowerNotPlayable::playedSmithyPower($player);
		}

		// EVENT EMITTED: UsedSmithyPower
	}

	public function useGraveyardPower(PlayerId $player)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw UseGraveyardPowerNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check this is a graveyard turn
		if (!$player->round()->isGraveyardTurn()) {
			throw UseGraveyardPowerNotPlayable::notAGraveyardTurn($player);
		}

		// Check they can afford to use the power (1 gold)
		if ($player->purse() < 1) {
			throw UseGraveyardPowerNotPlayable::cannotAfford($player, $player->purse());
		}

		// Check the district they are trying to use this power on is not the graveyard itself
		//TODO

		// EVENT EMITTED: UsedGraveyardPower
	}

	private function sizeOfCompletedCity(): int
	{
		// Ordinarily 8 but if the Bell Tower has been built - 7.
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
