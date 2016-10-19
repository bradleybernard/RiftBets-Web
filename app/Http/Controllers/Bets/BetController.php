<?php

namespace App\Http\Controllers\Bets;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;

class BetController extends Controller
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
		
	}

    public function question()
    {
    	DB::table('questions')->insert([
    		[
			'slug' 			=> 'test', 
			'description'	=> 'Who can code the best?',
			'type' 			=> 'choice',
			'multiplier' 	=> '3.0',
			'difficulty'	=> 'pretty easy'
			]
		]);
    }

    public function answer()
    {
    	DB::table('answers')->insert([
    		[
			'question_id' 	=> '1', 
			'game_id'		=> '12',
			'answer' 		=> 'Travis',
			]
		]);
    }
}
