<?php

namespace App\Http\Controllers\Questions;

use App\Http\Controllers\Controller;
use DB;

class QuestionsController extends Controller
{
    public function insertQuestions()
    {
        DB::table('questions')->truncate();
        DB::table('questions')->insert($this->getQuestions()->toArray());
    }

    public function getQuestions()
    {
        $functions = ['gameStats', 'gameTeamStats', 'gamePlayerStats'];
        $questions = collect([]);

        foreach($functions as $function) {
            $questions->push($this->{$function}());
        }

        return $questions->flatten(1);
    }

    private function gameStats()
    {
        return [
            [
                'slug'          => 'game_duration',
                'description'   => 'How long will the game last?',
                'type'          => 'time_duration',
                'multiplier'    => 99.99,
                'difficulty'    => 'impossible',
            ],
        ];
    }

    private function gameTeamStats()
    {
        return [
            [
                'slug'          => 'team_win', 
                'description'   => 'Which team will win?',
                'type'          => 'team_id',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_first_blood', 
                'description'   => 'Which team will get first blood?',
                'type'          => 'team_id',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_first_inhibitor', 
                'description'   => 'Which team will get first inhibitor?',
                'type'          => 'team_id',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_one_first_baron', 
                'description'   => 'Will %team_one% get first baron?',
                'type'          => 'boolean',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_two_first_baron', 
                'description'   => 'Will %team_two% get first baron?',
                'type'          => 'boolean',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_one_first_dragon', 
                'description'   => 'Will %team_one% get first dragon?',
                'type'          => 'boolean',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_two_first_dragon', 
                'description'   => 'Will %team_two% get first dragon?',
                'type'          => 'boolean',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_one_first_rift_herald', 
                'description'   => 'Will %team_one% get first rift herald?',
                'type'          => 'boolean',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_two_first_rift_herald', 
                'description'   => 'Will %team_two% get first rift herald?',
                'type'          => 'boolean',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_one_tower_kills', 
                'description'   => 'How many tower kills will %team_one% get?',
                'type'          => 'integer',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_two_tower_kills', 
                'description'   => 'How many tower kills will %team_two% get?',
                'type'          => 'integer',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_one_inhibitor_kills', 
                'description'   => 'How many inhibitor kills will %team_one% get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_two_inhibitor_kills', 
                'description'   => 'How many inhibitor kills will %team_two% get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_one_baron_kills', 
                'description'   => 'How many baron kills will %team_one% get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_two_baron_kills', 
                'description'   => 'How many baron kills will %team_two% get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_one_dragon_kills', 
                'description'   => 'How many dragon kills will %team_one% get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_two_dragon_kills', 
                'description'   => 'How many dragon kills will %team_two% get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_one_rift_herald_kills', 
                'description'   => 'How many rift herald kills will %team_one% get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_two_rift_herald_kills', 
                'description'   => 'How many rift herald kills will %team_two% get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_one_ban_first_champion', 
                'description'   => 'Which champion will %team_one% ban first?',
                'type'          => 'champion_id',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_two_ban_first_champion', 
                'description'   => 'Which champion will %team_two% ban first?',
                'type'          => 'champion_id',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_one_ban_second_champion', 
                'description'   => 'Which champion will %team_one% ban second?',
                'type'          => 'champion_id',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_two_ban_second_champion', 
                'description'   => 'Which champion will %team_two% ban second?',
                'type'          => 'champion_id',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_one_ban_third_champion', 
                'description'   => 'Which champion will %team_one% ban last?',
                'type'          => 'champion_id',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_two_ban_third_champion', 
                'description'   => 'Which champion will %team_two% ban last?',
                'type'          => 'champion_id',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_one_champion_bans', 
                'description'   => 'Which champions will %team_one% ban?',
                'type'          => 'champion_id_list_3',
                'multiplier'    => 10.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_two_champion_bans', 
                'description'   => 'Which champions will %team_two% ban?',
                'type'          => 'champion_id_list_3',
                'multiplier'    => 10.0,
                'difficulty'    => 'hard'
            ]
        ];
    }

    private function gamePlayerStats()
    {
        return [
            [
                'slug'          => 'team_one_player_top_champion',
                'description'   => 'Which champion will %team_one_top_player% play?',
                'type'          => 'champion_id',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_top_champion',
                'description'   => 'Which champion will %team_two_top_player% play?',
                'type'          => 'champion_id',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_jungle_champion',
                'description'   => 'Which champion will %team_one_jungle_player% play?',
                'type'          => 'champion_id',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_jungle_champion',
                'description'   => 'Which champion will %team_one_jungle_player% play?',
                'type'          => 'champion_id',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_mid_champion',
                'description'   => 'Which champion will %team_one_mid_player% play?',
                'type'          => 'champion_id',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_mid_champion',
                'description'   => 'Which champion will %team_two_mid_player% play?',
                'type'          => 'champion_id',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_adc_champion',
                'description'   => 'Which champion will %team_one_adc_player% play?',
                'type'          => 'champion_id',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_adc_champion',
                'description'   => 'Which champion will %team_two_adc_player% play?',
                'type'          => 'champion_id',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_support_champion',
                'description'   => 'Which champion will %team_one_support_player% play?',
                'type'          => 'champion_id',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_support_champion',
                'description'   => 'Which champion will %team_twoe_support_player% play?',
                'type'          => 'champion_id',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_top_kills',
                'description'   => 'How many kills will %team_one_top_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_top_kills',
                'description'   => 'How many kills will %team_two_top_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_jungle_kills',
                'description'   => 'How many kills will %team_one_jungle_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ], 
            [
                'slug'          => 'team_two_player_jungle_kills',
                'description'   => 'How many kills will %team_two_jungle_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_mid_kills',
                'description'   => 'How many kills will %team_one_mid_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_mid_kills',
                'description'   => 'How many kills will %team_two_mid_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_adc_kills',
                'description'   => 'How many kills will %team_one_adc_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_adc_kills',
                'description'   => 'How many kills will %team_two_adc_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_support_kills',
                'description'   => 'How many kills will %team_one_support_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_support_kills',
                'description'   => 'How many kills will %team_two_support_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_top_deaths',
                'description'   => 'How many deaths will %team_one_top_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_top_deaths',
                'description'   => 'How many deaths will %team_two_top_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_jungle_deaths',
                'description'   => 'How many deaths will %team_one_jungle_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_jungle_deaths',
                'description'   => 'How many deaths will %team_two_jungle_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_mid_deaths',
                'description'   => 'How many deaths will %team_one_mid_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_mid_deaths',
                'description'   => 'How many deaths will %team_two_mid_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_adc_deaths',
                'description'   => 'How many deaths will %team_one_adc_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_adc_deaths',
                'description'   => 'How many deaths will %team_two_adc_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_support_deaths',
                'description'   => 'How many deaths will %team_one_support_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_support_deaths',
                'description'   => 'How many deaths will %team_two_support_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_top_assists',
                'description'   => 'How many assists will %team_one_top_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_top_assists',
                'description'   => 'How many assists will %team_two_top_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_jungle_assists',
                'description'   => 'How many assists will %team_one_jungle_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_jungle_assists',
                'description'   => 'How many assists will %team_two_jungle_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_mid_assists',
                'description'   => 'How many assists will %team_one_mid_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_mid_assists',
                'description'   => 'How many assists will %team_two_mid_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_adc_assists',
                'description'   => 'How many assists will %team_one_adc_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_adc_assists',
                'description'   => 'How many assists will %team_two_adc_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_support_assists',
                'description'   => 'How many assists will %team_one_support_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_support_assists',
                'description'   => 'How many assists will %team_two_support_player% have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_top_gold',
                'description'   => 'How much gold will %team_one_top_player% earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_top_gold',
                'description'   => 'How much gold will %team_two_top_player% earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_jungle_gold',
                'description'   => 'How much gold will %team_one_jungle_player% earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_jungle_gold',
                'description'   => 'How much gold will %team_two_jungle_player% earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_mid_gold',
                'description'   => 'How much gold will %team_one_mid_player% earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_mid_gold',
                'description'   => 'How much gold will %team_two_mid_player% earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_adc_gold',
                'description'   => 'How much gold will %team_one_adc_player% earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_adc_gold',
                'description'   => 'How much gold will %team_two_adc_player% earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_support_gold',
                'description'   => 'How much gold will %team_one_support_player% earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_support_gold',
                'description'   => 'How much gold will %team_two_support_player% earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_top_minion_kills',
                'description'   => 'How much CS will %team_one_top_player% get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_top_minion_kills',
                'description'   => 'How much CS will %team_two_top_player% get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_jungle_minion_kills',
                'description'   => 'How much CS will %team_one_jungle_player% get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_jungle_minion_kills',
                'description'   => 'How much CS will %team_two_jungle_player% get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_mid_minion_kills',
                'description'   => 'How much CS will %team_one_mid_player% get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_mid_minion_kills',
                'description'   => 'How much CS will %team_two_mid_player% get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_adc_minion_kills',
                'description'   => 'How much CS will %team_one_adc_player% get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_adc_minion_kills',
                'description'   => 'How much CS will %team_two_adc_player% get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_support_minion_kills',
                'description'   => 'How much CS will %team_one_support_player% get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_support_minion_kills',
                'description'   => 'How much CS will %team_two_support_player% get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_top_items',
                'description'   => 'What items will %team_one_top_player% buy?',
                'type'          => 'item_id_list',
                'multiplier'    => 5.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_top_items',
                'description'   => 'What items will %team_two_top_player% buy?',
                'type'          => 'item_id_list',
                'multiplier'    => 5.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_jungle_items',
                'description'   => 'What items will %team_one_jungle_player% buy?',
                'type'          => 'item_id_list',
                'multiplier'    => 5.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_jungle_items',
                'description'   => 'What items will %team_two_jungle_player% buy?',
                'type'          => 'item_id_list',
                'multiplier'    => 5.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_mid_items',
                'description'   => 'What items will %team_one_mid_player% buy?',
                'type'          => 'item_id_list',
                'multiplier'    => 5.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_mid_items',
                'description'   => 'What items will %team_two_mid_player% buy?',
                'type'          => 'item_id_list',
                'multiplier'    => 5.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_adc_items',
                'description'   => 'What items will %team_one_adc_player% buy?',
                'type'          => 'item_id_list',
                'multiplier'    => 5.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_adc_items',
                'description'   => 'What items will %team_two_adc_player% buy?',
                'type'          => 'item_id_list',
                'multiplier'    => 5.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_support_items',
                'description'   => 'What items will %team_one_support_player% buy?',
                'type'          => 'item_id_list',
                'multiplier'    => 5.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_support_items',
                'description'   => 'What items will %team_two_support_player% buy?',
                'type'          => 'item_id_list',
                'multiplier'    => 5.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_champions',
                'description'   => 'What champions will %team_one% select?',
                'type'          => 'champion_id_list_5',
                'multiplier'    => 10.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_champions',
                'description'   => 'What champions will %team_two% select?',
                'type'          => 'champion_id_list_5',
                'multiplier'    => 10.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_top_spells',
                'description'   => 'What summoner spells will %team_one_top_player% use?',
                'type'          => 'summoner_id_list',
                'multiplier'    => 3.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_jungle_spells',
                'description'   => 'What summoner spells will %team_one_jungle_player% use?',
                'type'          => 'summoner_id_list',
                'multiplier'    => 3.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_mid_spells',
                'description'   => 'What summoner spells will %team_one_mid_player% use?',
                'type'          => 'summoner_id_list',
                'multiplier'    => 3.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_adc_spells',
                'description'   => 'What summoner spells will %team_one_adc_player% use?',
                'type'          => 'summoner_id_list',
                'multiplier'    => 3.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_support_spells',
                'description'   => 'What summoner spells will %team_one_support_player% use?',
                'type'          => 'summoner_id_list',
                'multiplier'    => 3.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_top_spells',
                'description'   => 'What summoner spells will %team_two_top_player% use?',
                'type'          => 'summoner_id_list',
                'multiplier'    => 3.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_jungle_spells',
                'description'   => 'What summoner spells will %team_two_jungle_player% use?',
                'type'          => 'summoner_id_list',
                'multiplier'    => 3.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_mid_spells',
                'description'   => 'What summoner spells will %team_two_mid_player% use?',
                'type'          => 'summoner_id_list',
                'multiplier'    => 3.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_adc_spells',
                'description'   => 'What summoner spells will %team_two_adc_player% use?',
                'type'          => 'summoner_id_list',
                'multiplier'    => 3.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_support_spells',
                'description'   => 'What summoner spells will %team_two_support_player% use?',
                'type'          => 'summoner_id_list',
                'multiplier'    => 3.00,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_total_gold',
                'description'   => 'How much total gold will %team_one% have?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra_hard',
            ],
            [
                'slug'          => 'team_two_total_gold',
                'description'   => 'How much total gold will %team_two% have?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra_hard',
            ],
        ];
    }
}   
