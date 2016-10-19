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
		$betId = DB::table('bets')->insertGetId([
			'user_id'			=> $request['user_id'],
			'credits_placed'	=> $request['credits_placed']
	    ]);

		DB::table('bet_details')->insert([
			'bet_id'			=> $betId,
			'game_id'			=> $request['game_id'],
			'question_id'		=> $request['question_id'],
			'user_answer'		=> $request['user_answer'],
			'credits_placed'	=> $request['credits_placed']
		]);
		
	}
}
