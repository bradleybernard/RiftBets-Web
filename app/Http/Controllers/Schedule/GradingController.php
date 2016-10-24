<?php

namespace App\Http\Controllers\Schedule;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class GradingController extends Controller
{
	public function grade()
	{
		// Things to update:
		// bet_details.is_complete, bets.is_complete, bet_details.answerId, bets.credits_won, 
		DB::table('bets')
		->where('bets.is_complete', 0)
		->join('bet_details', 'bet_details.bet_id', '=', 'bets.id')
		->join('question_answers', function($join){
			$join->on('bet_details.question_id', '=', 'question_answers.question_id')
				->whereColumn('question_answers.game_id', '=','bets.game_id');
		})
		->join('questions', 'questions.id', '=', 'question_answers.question_id')
		->whereNotNull('bets.game_id')
		->update([
			'bet_details.credits_won'	=> DB::raw('IF(bet_details.user_answer = question_answers.answer, bet_details.credits_placed * questions.multiplier, 0)'),
			'bet_details.is_complete'	=> true,
			'bet_details.won'			=> DB::raw('IF(bet_details.user_answer = question_answers.answer, True, False)'),
			'bet_details.answer_id'		=> DB::raw('question_answers.id'),
			// 'bets.is_complete'			=> DB::raw('IF(bets.bets_count = bets.bets_graded, 1, 0)'),
// 			'bets.credits_won'			=> DB::raw('SUM(bet_details.credits_won) AS credits_won 
// FROM bet_details INNER JOIN bets ON bet_details.bet_id=bets.id 
// GROUP BY bet_details.bet_id'),
			// 'bets.is_complete'			=> DB::raw('IF()')

		// DB::table('bets')
		// ->join()
		]);

		DB::table('bets')
		->select(['bet_details.bet_id', 'bets.*', 'sum(bet_details.credits_won) as credits_sum'])
		->join('bet_details', 'bet_details.bet_id', '=','bets.id')
		->groupBy('bet_details.bet_id')
		->update([
			'bets.credits_won'			=> DB::raw('credits_sum')
		]);
	}

	public function bets()
	{
		// game 1: rox vs skt
		// $gameId = 'fb741d06-d70c-4e08-b713-af9a1e8a7c62';
		// game 2: rox vs skt
		// $gameId = '3b124078-c557-4e55-a793-00cbd1b9dc0c';

		// Test data values
		$gameIdLong = 'eddd9430-f53c-4227-8b5f-bf4fb7b39f05';
		$gameId = '1001890201'; //gets filled in later but this is testing

		$bets = [
			// Game duration: 2920/60 = 48 mins
			[
				'question_id'       => 1,
				'user_answer'       => '2920',
				'credits_placed'    => 500
			],
			// team win: ROX
			[
				'question_id'       => 2,
				'user_answer'       => '100',
				'credits_placed'    => 500
			],
			// team first blood: rox
			[
				'question_id'       => 3,
				'user_answer'       => '100',
				'credits_placed'    => 500
			],
			// team first inhib: rox
			[
				'question_id'       => 4,
				'user_answer'       => '100',
				'credits_placed'    => 500
			],
			// team_one_dragon_kills (rox): 3
			[
				'question_id'       => 17,
				'user_answer'       => '3',
				'credits_placed'    => 500
			],
			// team_two_dragon_kills (skt): 1
			[
				'question_id'       => 18,
				'user_answer'       => '1',
				'credits_placed'    => 500
			],
			// team one ban first champ: ryze
			[
				'question_id'       => 21,
				'user_answer'       => '13',
				'credits_placed'    => 500
			],
			// team two ban second champ: sol
			[
				'question_id'       => 24,
				'user_answer'       => '136',
				'credits_placed'    => 500
			]
		];

		$betId = DB::table('bets')->insertGetId([
			'user_id'           => 1,
			'credits_placed'    => 10000,
			'api_game_id'       => $gameIdLong,
			'game_id'			=> $gameId,
		]);

		foreach($bets as &$bet) {
			$bet['bet_id']		= $betId;
		}

		DB::table('bet_details')->insert($bets);
	}
}
