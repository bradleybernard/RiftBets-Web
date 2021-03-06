<?php

namespace App\Http\Controllers\Bets;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;

use Validator;
use DB;

class BetsController extends Controller
{
	protected $games;

	public function bet(Request $request)
	{
		$request->merge(['user_credits' => $this->auth->user()->credits]);

		//retrieve if player has already input a bet on the game id specified
		$previousBet = DB::table('bets')
				->where('user_id', $this->auth->user()->id)
				->where('api_game_id', $request['bets'][0]['api_game_id'])
				->get();

		if(!$previousBet)
		{
			throw new \Dingo\Api\Exception\ResourceException('User has already bet on game');
		}

		//retrieve game ID from request
		$gameId = $request['bets'][0]['api_game_id'];

		//check if all game id's are consistent
		foreach ($request['bets'] as $entry) {
			if($entry['api_game_id'] != $gameId)
				throw new \Dingo\Api\Exception\ResourceException('Game ID must match for all bets'); 
		}

		//check if all contents are present in request
		//also validate basic things such as credits bet is positive number
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

        //collect and check if user has enough credits to make the bet
		$sum = collect($request->input('bets.*.credits_placed'))->sum();
		$request->merge(['credits_placed' => $sum]);

		$perBetMaximum = ($this->auth->user()->credits - count($request->input('bets.*'))) + 1;

		//validate that question exists and credits placed in the bet are within the acceptable range
		$validator = Validator::make($request->all(), [
		    'bets.*.question_slug' 		=> 'required|exists:questions,slug',
		    'bets.*.credits_placed' 	=> 'required|integer|min:1|max:' . $perBetMaximum,
		    'credits_placed'			=> 'required|integer|max:' . $this->auth->user()->credits,
		]);

		if ($validator->fails()) {
            throw new \Dingo\Api\Exception\ResourceException('Invalid request sent.', $validator->errors());
        }

        //gather match id
        $match = DB::table('games')->select('api_match_id')
        			->where('api_id_long', $request->input('bets.0.api_game_id'))
        			->first();

        //gather games in the match
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

		//check state of match to see if it's already resolved
		$matchState = DB::table('matches')->select('state')
						->where('api_id_long', $match->api_match_id)
						->first();

		if($matchState->state == 'resolved')
		{
			throw new \Dingo\Api\Exception\ResourceException('Match has already resolved.', $validator->errors());
		}

		$gameStart = DB::table('schedule')->select('scheduled_time')
						->where('api_match_id', $match->api_match_id)
						->first();

		$gameName = $games[$request->input('bets.0.api_game_id')]->name;

		$matchGames = DB::table('games')->select(['name as game_name', 'game_id'])
						->where('api_match_id', $match->api_match_id)
						->get()
						->unique('game_name')
						->keyBy('game_name');

		//compare game time scheduled to time bet is placed
		$mytime = Carbon::now();

		if ($gameName == 'G1')
		{
			$gameStart = Carbon::parse($gameStart->scheduled_time);

			$difference = $mytime->gt($gameStart);

			if ($difference){
				throw new \Dingo\Api\Exception\ResourceException('Invalid bet interval', $validator->errors());
			}
		} else
		{
			$prevGameName = 'G'.(($gameName[1])-1);

			if($matchGames[$prevGameName]->game_id == null)
			{
				throw new \Dingo\Api\Exception\ResourceException('Previous game has not resolved yet', $validator->errors());
			}

			$prevGame = DB::table('game_mappings')->select('created_at')
							->where('game_id', $matchGames[$gameName]->game_id)
							->first();

			$nextGame = Carbon::parse($prevGame->created_at);
			$nextGame->addMinutes(15);

			$difference = $mytime->gt($nextGame);

			if($difference){
				throw new \Dingo\Api\Exception\ResourceException('Invalid bet interval', $validator->errors());
			}
		}


		//insert data into bets table
		$betId = DB::table('bets')->insertGetId([
			'user_id'			=> $this->auth->user()->id,
			'credits_placed'	=> $request['credits_placed'],
			'api_game_id'		=> $request['bets'][0]['api_game_id'],
			'details_placed'	=> count($request['bets'])
		]);


		//assemble and insert data into bet_details table
		$questions = [];

		foreach ($request['bets'] as $bet) {
			array_push($questions, $bet['question_slug']);
		}

		$questionIds = DB::table('questions')->select('id')
						->whereIn('slug', $questions)
						->get();

		$details = [];

		for ($i=0; $i < count($request['bets']); $i++) {
			$details[$i]['question_id'] = $questionIds[$i]->id;
			$details[$i]['bet_id'] = $betId;
			$details[$i]['user_answer'] = $request['bets'][$i]['user_answer'];
			$details[$i]['credits_placed'] = $request['bets'][$i]['credits_placed'];
		}

		DB::table('bet_details')->insert($details);

		$matchId = DB::table('games')->select('api_match_id')
						->where('api_id_long', $request['bets'][0]['api_game_id'])
						->get();

		$matchId = $matchId[0]->api_match_id;

		DB::table('subscribed_users')->insert([
			'user_id' => $this->auth->user()->id, 'api_match_id' => $matchId
		]);
	}

}