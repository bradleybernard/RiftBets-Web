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
		$bets = DB::table('bets')
					->where('is_complete', false)
					->get();

		dd($bets);
	}

    public function question()
    {
    	DB::table('questions')->insert([
    		[
			'slug' 			=> 'test', 
			'description'	=> 'Which team will win?',
			'type' 			=> 'choice',
			'multiplier' 	=> '1.0',
			'difficulty'	=> 'easy'
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
