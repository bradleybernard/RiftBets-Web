<?php

namespace App\Http\Controllers\Queries;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class UserBetsController extends Controller
{
	public function query()
	{
		// User token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL3JpZnRiZXRzLmRldlwvYXBpXC9hdXRoXC9mYWNlYm9vayIsImlhdCI6MTQ3NzYyODk2MSwiZXhwIjoxNDc3NjMyNTYxLCJuYmYiOjE0Nzc2Mjg5NjEsImp0aSI6ImEzZTIwNzIzMDBiNGZhMDc4ZjIzMjE5NDBmMzUxZTFmIn0.G2IR96gw_Oem6GfFIF7KNgSiZKD4sdxLXUcr4WYbF5k
		$user = $this->auth->user();

		$select = [
			'bets.credits_placed', 'bets.credits_won as total_won', 'bet_details.credits_won',
			'bet_details.won', 'questions.description', 'question_answers.answer', 'bet_details.user_answer', 
			'bets.created_at as time_placed'
		];

		$bets = DB::table('users')
			->select($select)
			->join('bets', 'bets.user_id', '=', 'users.id')
			->join('bet_details', 'bet_details.bet_id', '=', 'bets.id')
			->join('questions', 'questions.id', '=', 'bet_details.question_id')
			->join('question_answers', 'question_answers.question_id', '=', 'bet_details.question_id')
			->where('users.id', $user->id)
			->get();
		
		return $this->response->array($bets);
	}
}