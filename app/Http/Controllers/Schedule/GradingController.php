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
    	// 
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
    		'bet_details.is_complete'	=> True,
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
    	$betId = DB::table('bets')->insertGetId([
            'user_id'           => 1,
            'credits_placed'    => 500,
            'bets_count'        => 1,
            'is_complete'       => False
        ]);

        DB::table('bet_details')->insert([
            'bet_id'            => $betId,
            'api_game_id'       => 'c151d2c2-a8a7-4b4d-b707-557cf9ba4fc7',
            // 'api_game_id'       => 'fb741d06-d70c-4e08-b713-af9a1e8a7c62',
            'question_id'       => 1,
            'user_answer'       => '2084',
            'credits_placed'    => 500
        ]);

        // DB::table('question_answers')->insert([
        //     'question_id'       =>  1,
        //     'game_id'           =>  1001890201,
        //     'answer'            => '2222'
        // ]);
    }
}
