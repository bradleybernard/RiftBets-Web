<?php

namespace App\Http\Controllers\Questions;

use App\Http\Controllers\Controller;
use DB;

class QuestionsController extends Controller
{
    public function insertQuestions()
    {
        DB::table('questions')->insert([
            [
                'slug'          => 'team_win', 
                'description'   => 'Which team will win?',
                'type'          => 'team',
                'multiplier'    => 1.0,
                'difficulty'    => 'easy'
            ],
            [
                'slug'          => 'player_top_team_one_champion',
                'description'   => 'Which champion will team one top lane play?',
                'type'          => 'champion',
                'multiplier'    => 1.25,
                'difficulty'    => 'medium',
            ],
            [
                'slug'          => 'game_duration',
                'description'   => 'How long will the game last?',
                'type'          => 'time',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ],
            [
                'slug'          => 'player_mid_team_one_kills',
                'description'   => 'How many kills will team one mid lane have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ]
        ]);
    }
}
