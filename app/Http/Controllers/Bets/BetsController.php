<?php

namespace App\Http\Controllers\Bets;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Validator;
use DB;

class BetsController extends Controller
{
	protected $games;

	private function isBeforeGameTime($games, $match)
	{

	}

	public function bet(Request $request)
	{
		/*
		bets[0][api_game_id]:eddd9430-f53c-4227-8b5f-bf4fb7b39f05
		bets[0][question_slug]:game_duration
		bets[0][user_answer]:2222
		bets[0][credits_placed]:500
		*/
		$request->merge(['user_credits' => $this->auth->user()->credits]);

		$validator = Validator::make($request->all(), [
			'bets.*'					=> 'required',
		    'bets.*.api_game_id' 		=> 'required|same:bets.*.api_game_id',
		    'bets.0.api_game_id'		=> 'exists:games,api_id_long',
		    'bets.*.question_slug' 		=> 'required|distinct',
		    'bets.*.user_answer' 		=> 'required',
		    'bets.*.credits_placed' 	=> 'required|integer|min:1',
		    'user_credits'				=> 'integer|min:' . count($request->input('bets.*')),
		]);

		if ($validator->fails()) {
            throw new \Dingo\Api\Exception\ResourceException('Invalid request sent.', $validator->errors());
        }

		$sum = collect($request->input('bets.*.credits_placed'))->sum();
		$request->merge(['credits_placed' => $sum]);

		$perBetMaximum = ($this->auth->user()->credits - count($request->input('bets.*'))) + 1;

		$validator = Validator::make($request->all(), [
		    'bets.*.question_slug' 		=> 'required|exists:questions,slug',
		    'bets.*.credits_placed' 	=> 'required|integer|min:1|max:' . $perBetMaximum,
		    'credits_placed'			=> 'required|integer|max:' . $this->auth->user()->credits,
		]);

		if ($validator->fails()) {
            throw new \Dingo\Api\Exception\ResourceException('Invalid request sent.', $validator->errors());
        }

        $match = DB::table('games')->select('api_match_id')
        			->where('api_id_long', $request->input('bets.0.api_game_id'))
        			->first();

        $games = DB::table('schedule')
        			->select('games.*')
					->join('matches', 'matches.api_id_long', '=', 'schedule.api_match_id')
					->join('games', 'games.api_match_id', '=', 'matches.api_id_long')
					->where('matches.api_id_long', $match->api_match_id)
					->get()
					->keyBy('api_id_long');

		if(!$games) {
			throw new \Dingo\Api\Exception\ResourceException('Invalid match ID.', $validator->errors());
		}

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
