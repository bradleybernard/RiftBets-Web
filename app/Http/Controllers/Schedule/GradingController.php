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
    	$bets = DB::table('bets')
    	->where('bets.is_complete', 0)
    	->join('bet_details', 'bet_details.bet_id', '=', 'bets.id')
    	->join('question_answers', function($join){
    		$join->on('bet_details.question_id', '=', 'question_answers.question_id')
    			->whereColumn('question_answers.game_id', '=','bet_details.game_id');
    	})
    	// ->join('question_answers', 'question_answers.game_id', '=', 'bet_details.game_id')
    	// ->whereColumn('bet_details.user_answer', 'question_answers.answer')
    	->join('questions', 'questions.id', '=', 'question_answers.question_id')
        ->whereNotNull('bet_details.game_id')
    	->update([
    		'bet_details.credits_won'	=> DB::raw('IF(bet_details.user_answer = question_answers.answer, bet_details.credits_placed * questions.multiplier, 0)'),
    		'bet_details.is_complete'	=> true,
    		'bet_details.win'			=> DB::raw('IF(bet_details.user_answer = question_answers.answer, True, False)'),
    		'bet_details.answer_id'		=> DB::raw('question_answers.id'),
    		'bets.bets_graded'			=> DB::raw('IF(bet_details.is_complete = 1 AND bet_details.id = bets.id AND bet_details.is_counted = 0, bets.bets_graded + 1, bets.bets_graded)'),
    		'bet_details.is_counted'	=> DB::raw('IF(bet_details.is_complete = 1, 1, 0)'),
    		'bets.is_complete'			=> DB::raw('IF(bets.bets_count = bets.bets_graded, 1, 0)'),
    		'bets.credits_won'			=> DB::raw('IF(bet_details.win = 1 AND bet_details.is_counted = 0, bets.credits_won + bet_details.credits_won, bets.credits_won)')
    		// 'bets.is_complete'			=> DB::raw('IF()')
    	]);
    }

    public function test()
    {
        // game 1: rox vs skt
        // $gameId = 'fb741d06-d70c-4e08-b713-af9a1e8a7c62';

        // game 2: rox vs skt
        // $gameId = '3b124078-c557-4e55-a793-00cbd1b9dc0c';

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
            // team two ban seocnd champ: sol
            [
                'question_id'       => 24,
                'user_answer'       => '136',
                'credits_placed'    => 500
            ]
        ];

    	$betId = DB::table('bets')->insertGetId([
            'user_id'           => 1,
            'credits_placed'    => 10000,
            'bets_count'        => count($bets),
        ]);

        foreach($bets as &$bet) {
            $bet['bet_id']            = $betId;
            $bet['api_game_id']       = $gameId;
        }

        DB::table('bet_details')->insert($bets);
    }
}
