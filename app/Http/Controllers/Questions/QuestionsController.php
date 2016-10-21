<?php

namespace App\Http\Controllers\Questions;

use App\Http\Controllers\Controller;
use DB;

class QuestionsController extends Controller
{
    public function insertQuestions()
    {
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
                'type'          => 'time',
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
                'type'          => 'team',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_first_blood', 
                'description'   => 'Which team will get first blood?',
                'type'          => 'team',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_first_inhibitor', 
                'description'   => 'Which team will get first inhibitor?',
                'type'          => 'team',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_one_first_baron', 
                'description'   => 'Will team one get first baron?',
                'type'          => 'team',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_two_first_baron', 
                'description'   => 'Will team two get first baron?',
                'type'          => 'team',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_one_first_dragon', 
                'description'   => 'Will team one get first dragon?',
                'type'          => 'team',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_two_first_dragon', 
                'description'   => 'Will team two get first dragon?',
                'type'          => 'team',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_one_first_rift_herald', 
                'description'   => 'Will team one get first rift herald?',
                'type'          => 'team',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_two_first_rift_herald', 
                'description'   => 'Will team two get first rift herald?',
                'type'          => 'team',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_one_tower_kills', 
                'description'   => 'How many tower kills will team one get?',
                'type'          => 'integer',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_two_tower_kills', 
                'description'   => 'How many tower kills will team two get?',
                'type'          => 'integer',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_one_inhibitor_kills', 
                'description'   => 'How many inhibitor kills will team one get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_two_inhibitor_kills', 
                'description'   => 'How many inhibitor kills will team two get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_one_baron_kills', 
                'description'   => 'How many baron kills will team one get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_two_baron_kills', 
                'description'   => 'How many baron kills will team two get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_one_dragon_kills', 
                'description'   => 'How many dragon kills will team one get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_two_dragon_kills', 
                'description'   => 'How many dragon kills will team two get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_one_rift_herald_kills', 
                'description'   => 'How many rift herald kills will team one get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_two_rift_herald_kills', 
                'description'   => 'How many rift herald kills will team two get?',
                'type'          => 'integer',
                'multiplier'    => 1.5,
                'difficulty'    => 'medium'
            ],
            [
                'slug'          => 'team_one_ban_first_champion', 
                'description'   => 'Which champion will team one ban first?',
                'type'          => 'champion',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_two_ban_first_champion', 
                'description'   => 'Which champion will team two ban first?',
                'type'          => 'champion',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_one_ban_second_champion', 
                'description'   => 'Which champion will team one ban second?',
                'type'          => 'champion',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_two_ban_second_champion', 
                'description'   => 'Which champion will team two ban second?',
                'type'          => 'champion',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_one_ban_third_champion', 
                'description'   => 'Which champion will team one ban last?',
                'type'          => 'champion',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_two_ban_third_champion', 
                'description'   => 'Which champion will team two ban last?',
                'type'          => 'champion',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],

        ];
    }

    private function gamePlayerStats()
    {
        return [
            [
                'slug'          => 'team_one_player_top_champion',
                'description'   => 'Which champion will team one Top play?',
                'type'          => 'champion',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_top_champion',
                'description'   => 'Which champion will team two Top play?',
                'type'          => 'champion',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_jungle_champion',
                'description'   => 'Which champion will team one Jungler play?',
                'type'          => 'champion',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_jungle_champion',
                'description'   => 'Which champion will team two Jungler play?',
                'type'          => 'champion',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_mid_champion',
                'description'   => 'Which champion will team one Mid play?',
                'type'          => 'champion',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_mid_champion',
                'description'   => 'Which champion will team two Mid play?',
                'type'          => 'champion',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_adc_champion',
                'description'   => 'Which champion will team one AD Carry play?',
                'type'          => 'champion',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_adc_champion',
                'description'   => 'Which champion will team two AD Carry play?',
                'type'          => 'champion',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_support_champion',
                'description'   => 'Which champion will team one Support play?',
                'type'          => 'champion',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_two_player_support_champion',
                'description'   => 'Which champion will team two Support play?',
                'type'          => 'champion',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_top_kills',
                'description'   => 'How many kills will team one Top have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_top_kills',
                'description'   => 'How many kills will team two Top have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_jungle_kills',
                'description'   => 'How many kills will team one Jungler have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ], 
            [
                'slug'          => 'team_two_player_jungle_kills',
                'description'   => 'How many kills will team two Jungler have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_mid_kills',
                'description'   => 'How many kills will team one Mid have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_mid_kills',
                'description'   => 'How many kills will team two Mid have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_adc_kills',
                'description'   => 'How many kills will team one AD Carry have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_adc_kills',
                'description'   => 'How many kills will team two AD Carry have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_support_kills',
                'description'   => 'How many kills will team one Support have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_support_kills',
                'description'   => 'How many kills will team two Support have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_top_deaths',
                'description'   => 'How many deaths will team one Top have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_top_deaths',
                'description'   => 'How many deaths will team two Top have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_jungle_deaths',
                'description'   => 'How many deaths will team one Jungler have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_jungle_deaths',
                'description'   => 'How many deaths will team two Jungler have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_mid_deaths',
                'description'   => 'How many deaths will team one Mid have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_mid_deaths',
                'description'   => 'How many deaths will team two Mid have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_adc_deaths',
                'description'   => 'How many deaths will team one AD Carry have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_adc_deaths',
                'description'   => 'How many deaths will team two AD Carry have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_support_deaths',
                'description'   => 'How many deaths will team one Support have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_support_deaths',
                'description'   => 'How many deaths will team two Support have?',
                'type'          => 'integer',
                'multiplier'    => 2.25,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_top_assists',
                'description'   => 'How many assists will team one Top have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_top_assists',
                'description'   => 'How many assists will team two Top have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_jungle_assists',
                'description'   => 'How many assists will team one Jungler have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_jungle_assists',
                'description'   => 'How many assists will team two Jungler have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_mid_assists',
                'description'   => 'How many assists will team one Mid have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_mid_assists',
                'description'   => 'How many assists will team two Mid have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_adc_assists',
                'description'   => 'How many assists will team one AD Carry have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_adc_assists',
                'description'   => 'How many assists will team two AD Carry have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_support_assists',
                'description'   => 'How many assists will team one Support have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_two_player_support_assists',
                'description'   => 'How many assists will team two Support have?',
                'type'          => 'integer',
                'multiplier'    => 2.50,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'team_one_player_top_gold',
                'description'   => 'How much gold will team one Top earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_top_gold',
                'description'   => 'How much gold will team two Top earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_jungle_gold',
                'description'   => 'How much gold will team one Jungler earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_jungle_gold',
                'description'   => 'How much gold will team two Jungler earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_mid_gold',
                'description'   => 'How much gold will team one Mid earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_mid_gold',
                'description'   => 'How much gold will team two Mid earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_adc_gold',
                'description'   => 'How much gold will team one AD Carry earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_adc_gold',
                'description'   => 'How much gold will team two AD Carry earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_support_gold',
                'description'   => 'How much gold will team one Support earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_support_gold',
                'description'   => 'How much gold will team two Support earn?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_top_minion_kills',
                'description'   => 'How much CS will team one Top get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_top_minion_kills',
                'description'   => 'How much CS will team two Top get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_jungle_minion_kills',
                'description'   => 'How much CS will team one Jungler get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_jungle_minion_kills',
                'description'   => 'How much CS will team two Jungler get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_mid_minion_kills',
                'description'   => 'How much CS will team one Mid get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_mid_minion_kills',
                'description'   => 'How much CS will team two Mid get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_adc_minion_kills',
                'description'   => 'How much CS will team one AD Carry get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_adc_minion_kills',
                'description'   => 'How much CS will team two AD Carry get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_one_player_support_minion_kills',
                'description'   => 'How much CS will team one Support get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ],
            [
                'slug'          => 'team_two_player_support_minion_kills',
                'description'   => 'How much CS will team two Support get?',
                'type'          => 'integer',
                'multiplier'    => 50.00,
                'difficulty'    => 'extra hard',
            ]       
        ];
    }
}   
