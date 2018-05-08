<?php

// @codingStandardsIgnoreFile

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use Funeralzone\FAS\Common\AggregateTestingTrait;
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    use AggregateTestingTrait;

    public function test_player_cannot_choose_character_when_not_their_turn()
    {

    }

    public function test_player_cannot_choose_character_when_already_chosen_character()
    {

    }

    public function test_player_cannot_choose_character_when_character_has_been_chosen_by_another_player()
    {

    }

    public function test_event_is_raised_when_player_chooses_character()
    {

    }

    public function test_on_character_chosen_state_of_game_changes()
    {

    }

    public function test_player_cannot_take_gold_when_not_their_turn()
    {

    }

    public function test_player_cannot_take_gold_when_its_a_graveyard_turn()
    {

    }

    public function test_player_cannot_take_gold_before_they_have_chosen_a_character()
    {

    }

    public function test_player_cannot_take_gold_if_they_have_already_completed_their_default_action()
    {

    }

    public function test_event_is_raised_when_player_takes_gold()
    {

    }

    public function test_on_take_gold_state_of_game_changes()
    {

    }

    public function test_player_cannot_draw_districts_when_not_their_turn()
    {

    }

    public function test_player_cannot_draw_districts_when_its_a_graveyard_turn()
    {

    }

    public function test_player_cannot_draw_districts_before_they_have_chosen_a_character()
    {

    }

    public function test_player_cannot_draw_districts_if_they_have_already_completed_their_default_action()
    {

    }

    public function test_event_is_raised_when_player_draws_districts()
    {

    }
}
