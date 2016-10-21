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
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
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
                'slug'          => 'team_first_baron', 
                'description'   => 'Which team will get first baron?',
                'type'          => 'team',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_first_dragon', 
                'description'   => 'Which team will get first dragon?',
                'type'          => 'team',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'team_first_rift_herald', 
                'description'   => 'Which team will get first win?',
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
                'description'   => 'Which champion will team one ban third?',
                'type'          => 'champion',
                'multiplier'    => 2.0,
                'difficulty'    => 'hard'
            ],
            [
                'slug'          => 'team_two_ban_third_champion', 
                'description'   => 'Which champion will team two ban third?',
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
                'description'   => 'Which champion will team one top lane play?',
                'type'          => 'champion',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'team_one_player_mid_kills',
                'description'   => 'How many kills will team one mid lane have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ]
        ];
    }
}
