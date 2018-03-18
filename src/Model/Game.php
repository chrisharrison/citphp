<?php

declare(strict_types=1);

namespace ChrisHarrison\Citphp;

use ChrisHarrison\Citphp\Model\Character;
use ChrisHarrison\Citphp\Model\Characters;
use ChrisHarrison\Citphp\Model\District;
use ChrisHarrison\Citphp\Model\DistrictColour;
use ChrisHarrison\Citphp\Model\Districts;
use ChrisHarrison\Citphp\Model\Events\CharacterChosen;
use ChrisHarrison\Citphp\Model\Events\CollectedBonusIncome;
use ChrisHarrison\Citphp\Model\Events\DistrictDestroyed;
use ChrisHarrison\Citphp\Model\Events\DistrictsBuilt;
use ChrisHarrison\Citphp\Model\Events\DistrictsChosen;
use ChrisHarrison\Citphp\Model\Events\DistrictsDrawn;
use ChrisHarrison\Citphp\Model\Events\GoldTaken;
use ChrisHarrison\Citphp\Model\Events\Murdered;
use ChrisHarrison\Citphp\Model\Events\SwappedHandWithDeck;
use ChrisHarrison\Citphp\Model\Events\SwappedHandWithPlayer;
use ChrisHarrison\Citphp\Model\Events\Theft;
use ChrisHarrison\Citphp\Model\Events\TurnEnded;
use ChrisHarrison\Citphp\Model\Events\UsedGraveyardPower;
use ChrisHarrison\Citphp\Model\Events\UsedLaboratoryPower;
use ChrisHarrison\Citphp\Model\Events\UsedSmithyPower;
use ChrisHarrison\Citphp\Model\Exceptions\BuildDistrictsNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\ChooseCharacterNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\ChooseDistrictsNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\CollectBonusIncomeNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\DestroyDistrictNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\DrawDistrictsNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\EndTurnNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\MurderNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\StealNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\SwapHandWithDeckNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\SwapHandWithPlayerNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\TakeGoldNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\UseGraveyardPowerNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\UseLaboratoryPowerNotPlayable;
use ChrisHarrison\Citphp\Model\Exceptions\UseSmithyPowerNotPlayable;
use ChrisHarrison\Citphp\Model\GameId;
use ChrisHarrison\Citphp\Model\GoldValue;
use ChrisHarrison\Citphp\Model\Player;
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
    public function chooseCharacter(PlayerId $playerId, Character $character): void
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
    public function takeGold(PlayerId $playerId): void
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

        $this->recordThat(GoldTaken::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @throws DrawDistrictsNotPlayable
     */
    public function drawDistricts(PlayerId $playerId): void
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
    public function chooseDistricts(PlayerId $playerId, Districts $districts): void
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

		if ($districts->count() > $maxNumberOfCardsToBeChosen) {
			throw ChooseDistrictsNotPlayable::tooManyDistrictsChosen($player, $districts->count(), $maxNumberOfCardsToBeChosen);
		}

        $this->recordThat(DistrictsChosen::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
            'districts' => $districts->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @param Districts $districts
     * @throws BuildDistrictsNotPlayable
     */
    public function buildDistricts(PlayerId $playerId, Districts $districts): void
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

		if ($player->round()->numberOfDistrictsBuilt() + $districts->count() > $maximumDistrictsThisRound) {
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

        $this->recordThat(DistrictsBuilt::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
            'districts' => $districts->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @param Character $victim
     * @throws MurderNotPlayable
     */
    public function murder(PlayerId $playerId, Character $victim): void
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
		if (!$player->round()->character()->isSame(Character::assassin())) {
			throw MurderNotPlayable::notTheAssassin($player);
		}

		// Check they haven't already exercised their power
		if ($player->round()->isSpecialPowerPlayed()) {
			throw MurderNotPlayable::specialPowerPlayed($player);
		}

        $this->recordThat(Murdered::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
            'victim' => $victim->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @param Character $victim
     * @throws StealNotPlayable
     */
    public function steal(PlayerId $playerId, Character $victim): void
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
		if (!$player->round()->character()->isSame(Character::thief())) {
			throw StealNotPlayable::notTheThief($player);
		}

		// Check they haven't already exercised their power
		if ($player->round()->isSpecialPowerPlayed()) {
			throw StealNotPlayable::specialPowerPlayed($player);
		}

        $this->recordThat(Theft::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
            'victim' => $victim->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @param PlayerId $victim
     * @throws SwapHandWithPlayerNotPlayable
     */
    public function swapHandWithPlayer(PlayerId $playerId, PlayerId $victim): void
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
		if (!$player->round()->character()->isSame(Character::magician())) {
			throw SwapHandWithPlayerNotPlayable::notTheMagician($player);
		}

		// Check they haven't already exercised their power
		if ($player->round()->isSpecialPowerPlayed()) {
			throw SwapHandWithPlayerNotPlayable::specialPowerPlayed($player);
		}

        $this->recordThat(SwappedHandWithPlayer::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
            'victimId' => $victim->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @param Districts $districts
     * @throws SwapHandWithDeckNotPlayable
     */
    public function swapHandWithDeck(PlayerId $playerId, Districts $districts): void
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
		if (!$player->round()->character()->isSame(Character::magician())) {
			throw SwapHandWithDeckNotPlayable::notTheMagician($player);
		}

		// Check they haven't already exercised their power
		if ($player->round()->isSpecialPowerPlayed()) {
			throw SwapHandWithDeckNotPlayable::specialPowerPlayed($player);
		}

		// Check they want to swap at least 1 card
		if ($districts->count() == 0) {
			throw SwapHandWithDeckNotPlayable::mustSwapAtLeastOne($player);
		}

		// Check they have the districts they want to swap in their hand
		if (!$player->hand()->hasAll($districts)) {
			throw SwapHandWithDeckNotPlayable::districtsMustBeInHand($player, $districts);
		}

        $this->recordThat(SwappedHandWithDeck::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
            'districts' => $districts->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @throws CollectBonusIncomeNotPlayable
     */
    public function collectBonusIncome(PlayerId $playerId): void
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
		if ($player->round()->isSpecialPowerPlayed()) {
			throw CollectBonusIncomeNotPlayable::specialPowerPlayed($player);
		}

        $this->recordThat(CollectedBonusIncome::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @param PlayerId $victimId
     * @param District $district
     * @throws DestroyDistrictNotPlayable
     */
    public function destroyDistrict(PlayerId $playerId, PlayerId $victimId, District $district): void
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
		if (!$player->round()->character()->isSame(Character::warlord())) {
			throw DestroyDistrictNotPlayable::notTheWarlord($player);
		}

		// Check they have completed their action
		if (!$player->round()->isDefaultActionCompleted()) {
			throw DestroyDistrictNotPlayable::defaultActionNotCompleted($player);
		}

		// Check they haven't already destroyed
		if ($player->round()->isDestroyDistrictPlayed()) {
			throw DestroyDistrictNotPlayable::destroyDistrictPlayed($player);
		}

		// Check the victim has the district in their city
		if (!$victim->city()->has($district)) {
			throw DestroyDistrictNotPlayable::victimHasNotBuiltDistrict($player);
		}

		// Check they can afford to destroy the district...
		// ...(taking into account that if the victim has built the Great Wall, the prices are higher)
		$priceModifier = GoldValue::fromNative(0);

		if ($victim->city()->has(District::greatWall())) {
            $priceModifier = GoldValue::fromNative(1);
		}

		if ($player->purse()->isLessThan($district->value()->withIncrement($priceModifier))) {
            throw DestroyDistrictNotPlayable::cannotAfford($player, $district);
        }

		// Check the victim's city is not complete [8 districts, 7 if bell tower]
		if ($victim->city()->count() >= $this->sizeOfCompletedCity()) {
			throw DestroyDistrictNotPlayable::completeCity($player, $victim);
		}

		// Check they are not trying to destroy a 'Keep' as it's indestructible
		if ($district->isSame(District::keep())) {
			throw DestroyDistrictNotPlayable::cannotDestroyKeep($player);
		}

        $this->recordThat(DistrictDestroyed::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
            'victimId' => $victimId->toNative(),
            'district' => $district->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @throws EndTurnNotPlayable
     */
    public function endTurn(PlayerId $playerId): void
	{
		$player = $this->players->byId($playerId);

		// Check if it's this player's turn
		if (!$player->round()->isTurn()) {
			throw EndTurnNotPlayable::notPlayersTurn($player, $this->players->current());
		}

        $this->recordThat(TurnEnded::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @param District $district
     * @throws UseLaboratoryPowerNotPlayable
     */
    public function useLaboratoryPower(PlayerId $playerId, District $district): void
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

		// Check they have built the Laboratory
		if (!$player->city()->has(District::laboratory())) {
			throw UseLaboratoryPowerNotPlayable::haveNotBuiltLaboratory($player);
		}

		// Check they have the district they want to discard in their hand
		if (!$player->hand()->has($district)) {
			throw UseLaboratoryPowerNotPlayable::districtNotInHand($player, $district);
		}

		// Check they have not already used this power
		if ($player->round()->isLaboratoryPowerPlayed()) {
			throw UseLaboratoryPowerNotPlayable::laboratoryPowerPlayed($player);
		}

        $this->recordThat(UsedLaboratoryPower::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
            'district' => $district->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @throws UseSmithyPowerNotPlayable
     */
    public function useSmithyPower(PlayerId $playerId): void
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
		if ($player->round()->isSmithyPowerPlayed()) {
			throw UseSmithyPowerNotPlayable::smithyPowerPlayed($player);
		}

        $this->recordThat(UsedSmithyPower::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
        ]));
	}

    /**
     * @param PlayerId $playerId
     * @throws UseGraveyardPowerNotPlayable
     */
    public function useGraveyardPower(PlayerId $playerId): void
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

        $this->recordThat(UsedGraveyardPower::occur($this->id->toNative(), [
            'playerId' => $playerId->toNative(),
        ]));
	}

    /**
     * @return int
     */
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

		return !$bellTowerBuilt ? 8 : 7;
	}

	private function merchantAdditionalGold(Player $player): void
    {
        // If the player is the merchant, the get additional gold after completing their default action
        if (!$player->round()->character()->isSame(Character::merchant())) {
            return;
        }
        $player = $player->withPurse($player->purse()->withIncrement(GoldValue::fromNative(1)));
        $this->players = $this->players->withPlayer($player);
        return;
    }

    private function onCharacterChosen(CharacterChosen $event): void
	{
		// Set the player's round to have chosen the character
        $player = $this->players->byId($event->playerId());
        $player = $player->withRound($player->round()->withCharacter($event->character()));
        $this->players = $this->players->withPlayer($player);

        // Remove the character from the deck
        $this->characterDeck = $this->characterDeck->remove(new Characters([$event->character()]));

		// Advance turn
        $this->players = $this->players->withTurnAdvanced();
	}

    private function onGoldTaken(GoldTaken $event): void
	{
        $player = $this->players->byId($event->playerId());

        // Increment the player's purse
        $player = $player->withPurse($player->purse()->withIncrement(GoldValue::fromNative(2)));

		// Set the player's round to have initiated and completed the default action
        $player = $player->withRound($player->round()->withDefaultActionCompleted());

        $this->merchantAdditionalGold($player);

        $this->players = $this->players->withPlayer($player);
	}

    private function onDistrcitsDrawn(DistrictsDrawn $event): void
	{
	    $player = $this->players->byId($event->playerId());

		// Draw districts from the district deck and put them in the player's potential hand
        $draw = $this->districtDeck->draw($event->numberDrawn());
        $this->districtDeck = $draw->deckNow();
        $player = $player->withRound($player->round()->withPotentialHand($draw->drawn()));

		// Set the player's round to have initiated the default action
        $player = $player->withRound($player->round()->withDefaultActionInitiated());

        $this->players = $this->players->withPlayer($player);
	}

    private function onDistrictsChosen(DistrictsChosen $event): void
	{
	    $player = $this->players->byId($event->playerId());

		// Put the districts in the player's hand. Any remaining districts in the potential hand go back in the district deck
        $remainingDeck = $player->round()->potentialHand()->remove($event->districts());
        $this->districtDeck = $this->districtDeck->putOnTop($remainingDeck);
        $player = $player->withRound($player->round()->withPotentialHand(new Districts([])));
        $player = $player->withHand($player->hand()->putOnTop($event->districts()));

		// Set the player's round to have completed the default action
        $player = $player->withRound($player->round()->withDefaultActionCompleted());

        $this->merchantAdditionalGold($player);

        $this->players = $this->players->withPlayer($player);
	}

    private function onDistrictsBuilt(DistrictsBuilt $event): void
	{
        $player = $this->players->byId($event->playerId());

		// Move the districts from the player's hand to the city
        $player = $player->withHand($player->hand()->remove($event->districts()));
        $player = $player->withCity($player->city()->putOnTop($event->districts()));

        // Decrement the player's purse
        $player = $player->withPurse($player->purse()->withDecrement($event->districts()->totalValue()));

		// Increment the number of districts built by this player this round
        $player = $player->withRound($player->round()->withIncrementedNumberOfDistrictsBuilt($event->districts()->count()));

        $this->players = $this->players->withPlayer($player);
	}

    private function onMurdered(Murdered $event): void
	{
        $player = $this->players->byId($event->playerId());

		// Set the victim to murdered
        $victim = $this->players->byCharacter($event->victim());
        $victim = $victim->withRound($victim->round()->murdered());

		// Set the player's round to have played the special power
        $player = $player->withRound($player->round()->withSpecialPowerPlayed());

        $this->players = $this->players->withPlayer($player);
        $this->players = $this->players->withPlayer($victim);
	}

    private function onTheft(Theft $event): void
	{
        $player = $this->players->byId($event->playerId());

		// Set the victim to thieved
        $victim = $this->players->byCharacter($event->victim());
        $victim = $victim->withRound($victim->round()->victimOfTheft());

		// Set the player's round to have played the special power
        $player = $player->withRound($player->round()->withSpecialPowerPlayed());

        $this->players = $this->players->withPlayer($player);
        $this->players = $this->players->withPlayer($victim);
	}

    private function onSwappedHandWithPlayer(SwappedHandWithPlayer $event): void
	{
        $player = $this->players->byId($event->playerId());
        $victim = $this->players->byId($event->victimId());

		// Give the player's hand to the victim and vice versa
        $playersHand = $player->hand();
        $victimsHand = $victim->hand();
        $player = $player->withHand($victimsHand);
        $victim = $player->withHand($playersHand);

		// Set the player's round to have played the special power
        $player->withRound($player->round()->withSpecialPowerPlayed());

        $this->players = $this->players->withPlayer($player);
        $this->players = $this->players->withPlayer($victim);
	}

    private function onSwappedHandWithDeck(SwappedHandWithDeck $event): void
	{
	    $player = $this->players->byId($event->playerId());

		// Put the chosen cards back in the district deck and draw new districts...
        // ...equal in number to those returned and put them in the player's hand
        $this->districtDeck = $this->districtDeck->putOnBottom($event->districts());
        $draw = $this->districtDeck->draw($event->districts()->count());
        $this->districtDeck = $draw->deckNow();
        $player = $player->withHand($player->hand()->putOnTop($draw->drawn()));

		// Set the player's round to have played the special power
        $player = $player->withRound($player->round()->withSpecialPowerPlayed());

        $this->players = $this->players->withPlayer($player);
	}

    private function onCollectedBonusIncome(CollectedBonusIncome $event): void
	{
	    $player = $this->players->byId($event->playerId());

		// Work out how much bonus income the player should receive
        $bonusIncome = 0;
        if ($player->round()->character()->isSame(Character::king())) {
            $bonusIncome = $player->city()->byDistrictColour(DistrictColour::YELLOW())->count();
        }
        if ($player->round()->character()->isSame(Character::bishop())) {
            $bonusIncome = $player->city()->byDistrictColour(DistrictColour::BLUE())->count();
        }
        if ($player->round()->character()->isSame(Character::merchant())) {
            $bonusIncome = $player->city()->byDistrictColour(DistrictColour::GREEN())->count();
        }
        if ($player->round()->character()->isSame(Character::warlord())) {
            $bonusIncome = $player->city()->byDistrictColour(DistrictColour::RED())->count();
        }

		// Increment the player's purse by the above amount
        $player = $player->withPurse($player->purse()->withIncrement(GoldValue::fromNative($bonusIncome)));

		// Set the player's round to have played the special power
        $player = $player->withRound($player->round()->withSpecialPowerPlayed());

        $this->players = $this->players->withPlayer($player);
	}

    private function onDistrictDestroyed(DistrictDestroyed $event): void
	{
        $player = $this->players->byId($event->playerId());
        $victim = $this->players->byId($event->victimId());

		// Remove the district from the victim's city
        $victim = $victim->withCity($victim->city()->remove(new Districts([$event->district()])));

        // Set that the player has destroyed a district
        $player = $player->withRound($player->round()->withDestroyDistrictPlayed());

        $this->players = $this->players->withPlayer($player);
        $this->players = $this->players->withPlayer($victim);
	}

    private function onTurnEnded(): void
	{
		// Advance turn
        $this->players = $this->players->withTurnAdvanced();

        // TODO: Initiate graveyard turn and others
	}

    private function onUsedLaboratoryPower(UsedLaboratoryPower $event): void
	{
	    $player = $this->players->byId($event->playerId());

		// Put the district back in the deck
        $this->districtDeck = $this->districtDeck->putOnBottom(new Districts([$event->district()]));

		// Increment the player's purse by one
        $player = $player->withPurse($player->purse()->withIncrement(GoldValue::fromNative(1)));

		// Set that the player has used this power
        $player = $player->withRound($player->round()->withLaboratoryPowerPlayed());

        $this->players = $this->players->withPlayer($player);
	}

    private function onUsedSmithyPower(UsedSmithyPower $event): void
	{
	    $player = $this->players->byId($event->playerId());

		// Decrement the player's purse by 2
        $player = $player->withPurse($player->purse()->withDecrement(GoldValue::fromNative(2)));

		// Draw 3 districts to the player's hand
        $draw = $this->districtDeck->draw(3);
        $this->districtDeck = $draw->deckNow();
        $player = $player->withHand($player->hand()->putOnTop($draw->drawn()));

		// Set that the player has used this power
        $player = $player->withRound($player->round()->withSmithyPowerPlayed());

        $this->players = $this->players->withPlayer($player);
	}

    private function onUsedGraveyardPower(UsedGraveyardPower $event): void
	{
        $player = $this->players->byId($event->playerId());

		// Restore district to player's city
        // TODO: Going to have to keep a reference to destroyed city(s) in the round

		// Decrement the player's purse
        $player = $player->withPurse($player->purse()->withDecrement(GoldValue::fromNative(1)));

        $this->players = $this->players->withPlayer($player);
	}
}
