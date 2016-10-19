<?php

namespace App\Http\Controllers\Bets;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;

class BetsController extends Controller
{
	public function placeBet(Request $request)
	{
		DB::table('bets')->insert([
			[
			'user_id'			=> $request['user_id'],
			'credits_placed'	=> $request['credits_placed']
			]
		]);

		$betId = DB::table('bets')->orderBy('created_at', 'desc')->first()->id;

		DB::table('bet_details')->insert([
			[
			'bet_id'			=> $betId,
			'game_id'			=> $request['game_id'],
			'question_id'		=> $request['question_id'],
			'user_answer'		=> $request['user_answer'],
			'credits_placed'	=> $request['credits_placed']
			]
		]);
		
	}

	public function gradeBet()
	{
		$bets = DB::table('bets')
					->where('is_complete', false)
					->get();

		dd($bets);
	}

    public function question()
    {
    	DB::table('questions')->insert([
			[
                'slug' 			=> 'team_win', 
    			'description'	=> 'Which team will win?',
    			'type' 			=> 'team',
    			'multiplier' 	=> 1.0,
    			'difficulty'	=> 'easy'
            ],
            [
                'slug'          => 'player_champion',
                'description'   => 'Which champion will player play?',
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
                'slug'          => 'player_kills',
                'description'   => 'How many kills will player have?',
                'type'          => 'integer',
                'multiplier'    => 2.00,
                'difficulty'    => 'hard',
            ]
		]);
    }

    public function answer()
    {
    	DB::table('answers')->insert([
    		[
    			'question_id' 	=> '1', 
    			'game_id'		=> '12',
    			'answer' 		=> 'TSM'
			]
		]);
    }
}
