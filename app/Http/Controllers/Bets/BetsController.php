<?php

namespace App\Http\Controllers\Bets;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;

class BetsController extends Controller
{
	public function bet(Request $request)
	{
		/*
		bets[0][api_game_id]:eddd9430-f53c-4227-8b5f-bf4fb7b39f05
		bets[0][question_slug]:game_duration
		bets[0][user_answer]:2222
		bets[0][credits_placed]:500
		*/

		$user = $this->auth->user();

		$games = DB::table('games')->whereIn('api_id_long', $request->input('bets.*.api_game_id'))->get();
		dd($games);

		$validator = Validator::make($request->all(), [
		    'bets.*.api_game_id' 		=> 'required|exists:games,api_id_long',
		    'bets.*.question_slug' 		=> 'required|exists:questions,slug',
		    'bets.*.user_answer' 		=> 'required',
		    'bets.*.credits_placed' 	=> 'required|integer|min:1|max:' . $user->credits,
		]);

		// $betId = DB::table('bets')->insertGetId([
		// 	'user_id'			=> $request['user_id'],
		// 	'credits_placed'	=> $request['credits_placed']
		// ]);

		// DB::table('bet_details')->insert([
		// 	'bet_id'			=> $betId,
		// 	'game_id'			=> $request['game_id'],
		// 	'question_id'		=> $request['question_id'],
		// 	'user_answer'		=> $request['user_answer'],
		// 	'credits_placed'	=> $request['credits_placed']
		// ]);
	}

}
