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
    	->update([
    		'bet_details.credits_won' => DB::raw('IF(bet_details.user_answer = question_answers.answer, bet_details.credits_placed * questions.multiplier, 0)'),
    	]);
    }

    public function test()
    {
    	$betId = DB::table('bets')->insertGetId([
    		'user_id'			=> 1,
    		'credits_placed'	=> 1200,
    		'is_complete'		=> False
		]);

		DB::table('bet_details')->insert([
			'bet_id'			=> $betId,
			'game_id'			=> 1001890201,
			'question_id'		=> 1,
			'user_answer'		=> '2222',
			'credits_placed'	=> 1200
		]);

		DB::table('question_answers')->insert([
			'question_id'		=>	1,
			'game_id'			=>	1001890201,
			'answer'			=> '2222'
		]);
    }
}
