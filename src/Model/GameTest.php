<?php

// @codingStandardsIgnoreFile

declare(strict_types=1);

namespace ChrisHarrison\Citphp\Model;

use ChrisHarrison\Citphp\Testing\AggregateTestingTrait;
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    use AggregateTestingTrait;

    public function test_player_cannot_choose_character_when_round_mode_is_invalid()
    {

    }

    public function test_player_cannot_choose_character_when_not_their_turn()
    {

    }

    public function test_player_cannot_choose_character_when_character_has_been_chosen_by_another_player()
    {

    }

    public function test_event_is_raised_when_player_chooses_character()
    {

    }

    public function test_player_cannot_take_gold_when_round_mode_is_invalid()
    {

    }

    public function test_player_cannot_take_gold_when_not_their_turn()
    {

    }

    public function test_player_cannot_take_gold_if_they_have_already_completed_their_default_action_this_round()
    {

    }

    public function test_event_is_raised_when_player_takes_gold()
    {

    }

    public function test_player_cannot_draw_districts_when_round_mode_is_invalid()
    {

    }

    public function test_player_cannot_draw_districts_when_not_their_turn()
    {

    }

    public function test_player_cannot_draw_districts_if_they_have_already_initiated_their_default_action_this_round()
    {

    }

    public function test_event_is_raised_when_player_draws_districts()
    {

    }

    public function test_player_cannot_choose_districts_when_round_mode_is_invalid()
    {

    }

    public function test_player_cannot_choose_districts_when_not_their_turn()
    {

    }

    public function test_player_cannot_choose_districts_if_they_have_already_initiated_their_default_action_this_round()
    {

    }

    public function test_player_cannot_choose_districts_that_were_not_previously_drawn()
    {

    }

    public function test_if_player_has_built_the_library_they_can_choose_a_maximum_of_2_districts()
    {

    }

    public function test_if_player_has_not_built_the_library_they_can_choose_a_maximum_of_1_district()
    {

    }

    public function test_event_is_raised_when_player_chooses_districts()
    {

    }

    public function test_player_cannot_build_districts_when_round_mode_is_invalid()
    {

    }

    public function test_player_cannot_build_districts_when_not_their_turn()
    {

    }

    public function test_player_cannot_build_districts_if_they_have_already_initiated_their_default_action_this_round()
    {

    }

    public function test_if_player_is_the_architect_they_can_build_a_maximum_of_3_districts_in_the_round()
    {

    }

    public function test_if_player_is_not_the_architect_they_can_build_a_maximum_of_2_districts_in_the_round()
    {

    }

    public function test_player_cannot_build_districts_that_are_not_in_their_hand()
    {

    }

    public function test_player_cannot_build_districts_they_cant_afford()
    {

    }

    public function test_player_can_build_a_maximum_of_1_duplicate_district_if_they_have_built_the_library()
    {

    }

    public function test_player_can_build_no_duplicate_districts_if_they_have_not_built_the_library()
    {

    }

    public function test_event_is_raised_when_player_builds_districts()
    {

    }

    public function test_player_cannot_murder_when_round_mode_is_invalid()
    {

    }

    public function test_player_cannot_murder_when_not_their_turn()
    {

    }

    public function test_player_cannot_murder_if_already_played_special_power_this_round()
    {

    }

    public function test_player_cannot_murder_if_they_are_not_the_assassin()
    {

    }

    public function test_event_is_raised_when_player_commits_murder()
    {

    }

    public function test_player_cannot_steal_when_round_mode_is_invalid()
    {

    }

    public function test_player_cannot_steal_when_not_their_turn()
    {

    }

    public function test_player_cannot_steal_if_already_played_special_power_this_round()
    {

    }

    public function test_player_cannot_steal_if_they_are_not_the_thief()
    {

    }

    public function test_event_is_raised_when_player_steals()
    {

    }

    public function test_player_cannot_swap_hand_with_player_when_round_mode_is_invalid()
    {

    }

    public function test_player_cannot_swap_hand_with_player_when_not_their_turn()
    {

    }

    public function test_player_cannot_swap_hand_with_player_if_already_played_special_power_this_round()
    {

    }

    public function test_player_cannot_swap_hand_with_player_if_they_are_not_the_magician()
    {

    }

    public function test_event_is_raised_when_player_swaps_hand_with_player()
    {

    }

    public function test_player_cannot_swap_hand_with_deck_when_round_mode_is_invalid()
    {

    }

    public function test_player_cannot_swap_hand_with_deck_when_not_their_turn()
    {

    }

    public function test_player_cannot_swap_hand_with_deck_if_already_played_special_power_this_round()
    {

    }

    public function test_player_cannot_swap_hand_with_deck_if_they_are_not_the_magician()
    {

    }

    public function test_player_cannot_swap_hand_with_deck_if_they_do_not_select_at_least_1_card()
    {

    }

    public function test_player_cannot_swap_hand_with_deck_if_they_select_cards_that_are_not_in_their_hand()
    {

    }

    public function test_event_is_raised_when_player_swaps_hand_with_deck()
    {

    }

    public function test_player_cannot_collect_bonus_income_when_round_mode_is_invalid()
    {

    }

    public function test_player_cannot_collect_bonus_income_when_not_their_turn()
    {

    }

    public function test_player_cannot_collect_bonus_income_if_already_played_special_power_this_round()
    {

    }

    public function test_player_cannot_collect_bonus_income_if_they_are_not_the_king_bishop_merchant_or_warlord()
    {

    }

    public function test_event_is_raised_when_player_collects_bonus_income()
    {

    }

    public function test_player_cannot_destroy_district_when_round_mode_is_invalid()
    {

    }

    public function test_player_cannot_destroy_district_when_not_their_turn()
    {

    }

    public function test_player_cannot_destroy_district_if_not_playing_the_warlord()
    {

    }

    public function test_player_cannot_destroy_district_if_they_have_not_completed_their_default_action()
    {

    }

    public function test_player_cannot_destroy_district_if_they_have_already_destroyed_something_this_round()
    {

    }

    public function test_player_cannot_destroy_district_if_they_are_not_the_warlord()
    {

    }

    public function test_player_cannot_destroy_district_if_the_victim_does_not_have_target_in_their_city()
    {

    }

    public function test_player_cannot_destroy_district_if_they_cannot_afford_it()
    {

    }

    public function test_player_cannot_destroy_district_if_victims_city_is_complete()
    {

    }

    public function test_player_cannot_destroy_the_keep()
    {

    }

    public function test_event_is_raised_when_player_destroys_district()
    {

    }

    public function test_player_cannot_end_their_turn_when_its_not_their_turn()
    {

    }

    public function test_event_is_raised_when_player_ends_turn()
    {

    }

    public function test_player_cannot_use_the_laboratory_power_when_round_mode_is_invalid()
    {

    }

    public function test_player_cannot_use_the_laboratory_power_when_not_their_turn()
    {

    }

    public function test_player_cannot_use_the_laboratory_power_if_they_have_not_built_the_laboratory()
    {

    }

    public function test_player_cannot_use_the_laboratory_power_with_a_district_they_do_not_have_in_their_hand()
    {

    }

    public function test_player_cannot_use_the_laboratory_power_if_they_have_already_used_it_in_this_round()
    {

    }

    public function test_event_is_raised_when_player_uses_laboratory_power()
    {

    }

    public function test_player_cannot_use_the_smithy_power_when_round_mode_is_invalid()
    {

    }

    public function test_player_cannot_use_the_smithy_power_when_not_their_turn()
    {

    }

    public function test_player_cannot_use_the_smithy_power_if_they_have_not_built_the_smithy()
    {

    }

    public function test_player_cannot_use_the_smithy_power_if_they_cant_afford_it()
    {

    }

    public function test_player_cannot_use_the_smithy_power_if_they_have_already_used_it_in_this_round()
    {

    }

    public function test_event_is_raised_when_player_uses_smithy_power()
    {

    }

    public function test_player_cannot_use_the_graveyard_power_when_round_mode_is_invalid()
    {

    }

    public function test_player_cannot_use_the_graveyard_power_when_not_their_turn()
    {

    }

    public function test_event_is_raised_when_player_uses_graveyard_power()
    {

    }

    public function test_the_size_of_a_completed_city_is_8_when_bell_tower_not_built_in_any_city()
    {

    }

    public function test_the_size_of_a_completed_city_is_7_when_bell_tower_has_been_built_in_any_city()
    {

    }
}
