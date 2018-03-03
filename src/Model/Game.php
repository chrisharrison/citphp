<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp;

use ChrisHarrison\Citphp\Model\Character;
use ChrisHarrison\Citphp\Model\Characters;
use ChrisHarrison\Citphp\Model\District;
use ChrisHarrison\Citphp\Model\Districts;
use ChrisHarrison\Citphp\Model\Events\CharacterChosen;
use ChrisHarrison\Citphp\Model\Events\DistrictsDrawn;
use ChrisHarrison\Citphp\Model\Exceptions\BuildDistrictsNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\ChooseCharacterNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\ChooseDistrictsNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\DrawDistrictsNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\TakeGoldNotPlayable;
use ChrisHarrison\Citphp\Model\GameId;
use ChrisHarrison\Citphp\Model\PlayerId;
use ChrisHarrison\Citphp\Model\Players;
use Prooph\EventSourcing\Aggregate\EventProducerTrait;
use Prooph\EventSourcing\Aggregate\EventSourcedTrait;

final class Game
{
	use EventProducerTrait;
	use EventSourcedTrait;

    /**
     * @var GameId
     */
    private $id;

    /**
     * @var Players
     */
    private $players;

    /**
     * @var Characters
     */
    private $characterDeck;

    /**
     * @var Districts
     */
    private $districtDeck;

    /**
     * @param PlayerId $playerId
     * @param Character $character
     * @throws ChooseCharacterNotPlayable
     */
    public function chooseCharacter(PlayerId $playerId, Character $character)
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw ChooseCharacterNotPlayable::notPlayersTurn($player, $this->players->current());
		}

		// Check the player has not already chosen a character
		if ($player->round()->hasChosenCharacter()) {
			throw ChooseCharacterNotPlayable::alreadyPlayed($player);
		}

		// Check if the character has not already been chosen
		if (!$this->characterDeck->has($character)) {
			throw ChooseCharacterNotPlayable::characterHasBeenDrawn($player, $character);
		}

		$this->recordThat(CharacterChosen::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
            'character' => $character->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @throws TakeGoldNotPlayable
     */
    public function takeGold(PlayerId $playerId)
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
		if ($player->round()->isDefaultActionInitiated()) {
			throw TakeGoldNotPlayable::defaultActionInitiated($player);
		}

        $this->recordThat(CharacterChosen::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @throws DrawDistrictsNotPlayable
     */
    public function drawDistricts(PlayerId $playerId)
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
		if ($player->round()->isDefaultActionInitiated()) {
			throw DrawDistrictsNotPlayable::defaultActionInitiated($player);
		}

        // If the player has built the Observatory district, they can draw 3 cards, else 2
        $numberDrawn = 2;
        if ($player->city()->has(District::observatory())) {
            $numberDrawn = 3;
        }

        $this->recordThat(DistrictsDrawn::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
            'numberDrawn' => $numberDrawn,
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @param Districts $districts
     * @throws ChooseDistrictsNotPlayable
     */
    public function chooseDistricts(PlayerId $playerId, Districts $districts)
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
		if (!$player->round()->potentialHand()->hasAll($districts)) {
			throw ChooseDistrictsNotPlayable::notInHand($player, $districts);
		}

		// Check they haven't already completed their default action
		if ($player->round()->isDefaultActionCompleted()) {
			throw ChooseDistrictsNotPlayable::defaultActionCompleted($player);
		}

		// If the player has built the Library district, they can choose 2 cards, else 1
		$maxNumberOfCardsToBeChosen = 1;
		if ($player->city()->has(District::library())) {
			$maxNumberOfCardsToBeChosen = 2;
		}

		if (count($districts) > $maxNumberOfCardsToBeChosen) {
			throw ChooseDistrictsNotPlayable::tooManyDistrictsChosen($player, count($districts), $maxNumberOfCardsToBeChosen);
		}

        $this->recordThat(DistrictsDrawn::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
            'districts' => $districts->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @param Districts $districts
     * @throws BuildDistrictsNotPlayable
     */
    public function buildDistricts(PlayerId $playerId, Districts $districts)
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
		if (!$player->round()->isDefaultActionCompleted()) {
			throw BuildDistrictsNotPlayable::defaultActionNotCompleted($player);
		}

		// Check they haven't already built their maximum districts this round (if playing architect it's 3, else 1)
		if ($player->round()->character()->isSame(Character::architect())) {
			$maximumDistrictsThisRound = 3;
		} else {
			$maximumDistrictsThisRound = 1;
		}

		if ($player->round()->numberOfDistrictsBuilt() + count($districts) > $maximumDistrictsThisRound) {
			throw BuildDistrictsNotPlayable::willExceedMaximumDistricts($player, $maximumDistrictsThisRound);
		}

		// Check they have the districts in their hand
		if (!$player->hand()->hasAll($districts)) {
			throw BuildDistrictsNotPlayable::notInHand($player, $districts);
		}

		// Check they can afford to build the districts
		if ($districts->totalValue()->isMoreThan($player->purse())) {
			throw BuildDistrictsNotPlayable::cannotAfford($player, $districts->totalValue());
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

        $this->recordThat(DistrictsDrawn::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
            'districts' => $districts->toNative(),
        ]));
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
			throw DestroyDistrictNotPlayable::cannotAfford($player, $district);
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
			throw UseSmithyPowerNotPlayable::cannotAfford($player);
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
			throw UseGraveyardPowerNotPlayable::cannotAfford($player);
		}

		// The district they are trying to use this power on cannot be the graveyard itself
		// TODO

		// EVENT EMITTED: UsedGraveyardPower
	}

	private function sizeOfCompletedCity(): int
	{
		// Ordinarily 8 but if the Bell Tower has been built - 7.
		$bellTowerBuilt = false;

		foreach ($this->players as $player) {
			if ($player->city->has(District::bellTower())) {
				$bellTowerBuilt = true;
				break;
			}
		}

		return $bellTowerBuilt;
	}

	private function onCharacterChosen(CharacterChosen $event)
	{
		// Set the player's round to have chosen the character
		// Advance turn
	}

	private function onGoldTaken()
	{
		// Increment the player's purse
		// Set the player's round to have initiated and completed the default action
	}

	private function onDistrcitsDrawn()
	{
		// Draw districts from the district deck and put them in the player's potential hand
		// Set the player's round to have initiated the default action
	}

	private function onDistrictsChosen()
	{
		// Put the districts in the player's hand. Any remaining districts in the potential hand go back in the district deck
		// Set the player's round to have completed the default action
	}

	private function onDistrictsBuilt()
	{
		// Move the districts from the player's hand to the city
		// Increment the number of districts built by this player this round
	}

	private function onMurdered()
	{
		// Set the victim to murdered
		// Set the player's round to have played the special power
	}

	private function onTheft()
	{
		// Set the victim to thieved
		// Set the player's round to have played the special power
	}

	private function onSwappedHandWithPlayer()
	{
		// Give the player's hand to the victim and viceversa
		// Set the player's round to have played the special power
	}

	private function onSwappedHandWithDeck()
	{
		// Put the chosen cards back in the district deck and draw new districts equal in number to those returned and put them in the player's hand
		// Set the player's round to have played the special power
	}

	private function onCollectedBonusIncome()
	{
		// Work out how much bonus income the player should receive
		// Increment the player's purse by the above amount
		// Set the player's round to have played the special power
	}

	private function onDistrictDestroyed()
	{
		// Remove the district from the victim's city
	}

	private function onTurnEnded()
	{
		// Advance turn
	}

	private function onUsedLaboratoryPower()
	{
		// Put the district back in the deck
		// Increment the player's purse by one
		// Set that the player has used this power
	}

	private function onUsedSmithyPower()
	{
		// Decrement the player's purse by 2
		// Draw 3 districts to the player's hand
		// Set that the player has used this power
	}

	private function onUsedGraveyardPower()
	{
		// Restore district to player's city
		// Decrement the player's purse
	}
}
