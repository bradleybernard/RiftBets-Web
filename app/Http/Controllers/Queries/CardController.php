<?php

namespace App\Http\Controllers\Queries;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use Validator;
use \Carbon\Carbon;

class CardController extends Controller
{
    public function generate(Request $request)
    {
    	//$request.keys = [match_id, game_id, question_count, difficulty]
    	// For now only uses api_game_id
    	$input = collect([
    		'user_id'			=> $this->auth->user()->id,
    		'match_id'			=> $request->input('match_id', null),
    		'api_game_id'		=> $request->input('api_game_id', null),
    		'question_count'	=> $request->input('question_count'),
    		'difficulty'		=> $request->input('difficulty', null),
    	]);

    	/* Need to add Validation */
    		
    	$questions = DB::table('questions')->select(['id as question_id', 'slug', 'difficulty', 'type', 'description'])
    					->get();

    	$defaultQuestion = $questions->get('1');

    	$questions->forget('1');

    	if($input['difficulty'] != null)
    	{
    		$questions = $questions->where('difficulty', $input['difficulty']);
    	}

    	$questions = $questions->random($input['question_count']);

    	$questions->prepend($defaultQuestion);

    	$card = (object) [];
    	$card->user_id = $input['user_id'];
    	$card->questions = $questions->toArray();

    	$card->champions = DB::table('ddragon_champions')->select(['api_id', 'champion_name', 'image_url'])
    					->get()
    					->toArray();

    	$card->items = DB::table('ddragon_items')->select(['api_id', 'name as item_name', 'image_url'])
    					->get()
    					->toArray();

    	$cardId = DB::table('cards')->insertGetId([
    		'user_id'			=> $card->user_id,
    		'api_game_id'		=> $input['api_game_id'],
    		'details_placed'	=> $input['question_count'],
    		'created_at'		=> Carbon::now()->toDateTimeString(),
    	]);

    	foreach ($questions as $question)
    	{
    		DB::table('card_details')->insert([
    			'card_id'		=> $cardId,
    			'question_id'	=> $question->question_id,
    			'created_at'	=> Carbon::now()->toDateTimeString(),
    		]);
    	}


    	return $this->response->array((array)$card);

    }

    public function reroll(Request $request)
    {

    }
}
