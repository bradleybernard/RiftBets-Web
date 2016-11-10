<?php

namespace App\Http\Controllers\Schedule;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use DB;

class GradingController extends Controller
{
	public function grade()
	{
		DB::table('bets')
		->where('bets.is_complete', 0)
		->join('bet_details', 'bet_details.bet_id', '=', 'bets.id')
		->join('question_answers', function($join){
			$join->on('bet_details.question_id', '=', 'question_answers.question_id')
				->whereColumn('question_answers.game_id', '=','bets.game_id');
		})
		->join('questions', 'questions.id', '=', 'question_answers.question_id')
		->join('users', 'users.id', '=', 'bets.user_id')
		->whereNotNull('bets.game_id')
		->update([
			'bet_details.credits_won'	=> DB::raw('IF(bet_details.user_answer = question_answers.answer, bet_details.credits_placed * questions.multiplier, 0)'),
			'bet_details.is_complete'	=> true,
			'bet_details.won'			=> DB::raw('IF(bet_details.user_answer = question_answers.answer, 1, 0)'),
			'bet_details.answer_id'		=> DB::raw('question_answers.id'),
			'users.bets_placed'			=> DB::raw('users.bets_placed+1')
		]);

		DB::update('UPDATE bets 
			INNER JOIN bet_details ON bet_details.bet_id = bets.id
			INNER JOIN users ON users.id = bets.user_id
			SET bets.is_complete = 1,
				bets.won = IF((SELECT SUM(bet_details.won) FROM bet_details where bet_id = bets.id) = bets.details_placed, 1, 0),
				bets.credits_won = (SELECT SUM(bet_details.credits_won)FROM bet_details WHERE bet_id = bets.id)WHERE bets.is_complete = 0 AND bet_details.is_complete = 1'
				);

		DB::table('bets')
		->where('bets.is_counted', 0)
		->join('users', 'users.id', '=', 'bets.user_id')
		->join('user_stats', 'user_stats.user_id', '=', 'users.id')
		->update([
			'bets.is_counted'				=> true,

			'user_stats.bets_won' 			=> DB::raw('IF (bets.won = 1, user_stats.bets_won + 1, user_stats.bets_won)'),
            'user_stats.bets_lost' 			=> DB::raw('IF (bets.won = 0, user_stats.bets_lost + 1, user_stats.bets_lost)'),
            'user_stats.bets_complete' 		=> DB::raw('user_stats.bets_complete + 1'),

            'user_stats.weekly_streak' 		=> DB::raw('IF (bet_details.won = 1, user_stats.weekly_streak + 1, 0)'),
            'user_stats.monthly_streak' 	=> DB::raw('IF (bet_details.won = 1, user_stats.monthly_streak + 1, 0)'),
            'user_stats.alltime_streak' 	=> DB::raw('IF (bet_details.won = 1, user_stats.alltime_streak + 1, 0)'),

            'user_stats.weekly_wins' 		=> DB::raw('IF (bet_details.won = 1, user_stats.weekly_wins + 1, user_stats.weekly_wins)'),
            'user_stats.monthly_wins' 		=> DB::raw('IF (bet_details.won = 1, user_stats.monthly_wins + 1, user_stats.monthly_wins)'),
            'user_stats.alltime_wins' 		=> DB::raw('IF (bet_details.won = 1, user_stats.alltime_wins + 1, user_stats.alltime_wins)'),
            'user_stats.redis_update' 		=> 1,

			'users.credits'					=> DB::raw('users.credits+bets.credits_won'),
			'users.bets_won'				=> DB::raw('IF(bets.won = 1, users.bets_won+1, users.bets_won)'),
		]);
	}

	public function resetWeekly()
	{
		DB::table('users')
		->update([
			'users.bets_won_weekly'	=> 0,
		]);
	}

	public function resetMonthly()
	{
		DB::table('users')
		->update([
			'users.bets_won_monthly'	=> 0,
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
				'user_answer'       => '2222',
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
			'details_placed'	=> 8
		]);

		foreach($bets as &$bet) {
			$bet['bet_id']		= $betId;
		}

		DB::table('bet_details')->insert($bets);
	}
}
