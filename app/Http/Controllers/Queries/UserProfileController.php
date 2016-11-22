<?php

namespace App\Http\Controllers\Queries;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;


class UserProfileController extends Controller
{
    public function query(Request $request)
    {

    	//Games -> bets -> bet details
    	//Order by most recent bets

    	// dd($request->user_id);

    	$user = DB::table('users')
    			->where('id', '=', $request->user_id)
    			->get()[0];

		// dd($user);

		$bets = DB::table('bets')
				->where('bets.user_id', '=', $user->id)
				->get();
		$bets = $bets->keyBy('api_game_id');

		// dd($bets);

		$details = DB::table('bet_details')
				->join('questions', 'questions.id', '=', 'bet_details.question_id')
				->join('question_answers', 'question_answers.question_id', '=', 'bet_details.question_id')
				->whereIn('bet_details.bet_id', $bets->pluck('id'))
				->get();
		$details = $details->groupBy('bet_id');

		// dd($details);

		$bets->transform(function ($item, $key) use($details)
		{
			$item->details = $details[$item->id];
			return $item;
		});

		// dd($bets);

		$games = DB::table('games')
				->whereIn('api_id_long', $bets->pluck('api_game_id'))
				->get();

		$games->transform(function ($item, $key) use($bets)
		{
			$item->bet = $bets[$item->api_id_long];
			return $item;
		});

		// dd($games);

		// dd($details);

		//  DB::table('users')
			// ->select($select)
			// ->join('bets', 'bets.user_id', '=', 'users.id')
			// ->join('bet_details', 'bet_details.bet_id', '=', 'bets.id')
			// ->join('questions', 'questions.id', '=', 'bet_details.question_id')
			// ->join('question_answers', 'question_answers.question_id', '=', 'bet_details.question_id')
			// ->where('users.id', $user->id)
			// ->get();

		$userStats = DB::table('user_stats')
			->where('id', '=', $user->id)
			->get();

		$userInfo = [
			'name'			=> $user->name,
			'credits'		=> $user->credits,
			'email'			=> $user->email,
			'created_at'	=> $user->created_at
		];

		$profile = array('user_info' => $userInfo, 'user_stats' => $userStats, 'games' => $games);
		
		return $this->response->array($profile);
    }
}
