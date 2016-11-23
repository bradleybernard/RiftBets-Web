<?php

namespace App\Http\Controllers\Queries;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
    	/*$input = collect([
    		'user_id'			=> $this->auth->user()->id,
    		'match_id'			=> $request->input('match_id', null),
    		'api_game_id'		=> $request->input('api_game_id', null),
    		'question_count'	=> $request->input('question_count'),
    		'difficulty'		=> $request->input('difficulty', null),
    		'reroll'			=> $request->input('reroll', false)
    	]); */

    	/* Need to add Validation */
    	$validator = Validator::make($request->all(), [
            'api_game_id'       => 'exists:games,api_id_long',
            'question_count'    => 'required'    
        ]);

        $checkCardExists = DB::table('cards')
                                    ->where('user_id', $this->auth->user()->id)
                                    ->where('api_game_id', $request->input('api_game_id'))
                                    ->get()
                                    ->first();

        if (!$request->input('reroll', false))
        {   
            if($checkCardExists)
                throw new \Dingo\Api\Exception\ResourceException('Card already exists. Please reroll.'); 

            DB::table('card_rerolls')->insert([
                'user_id'       => $this->auth->user()->id,
                'api_game_id'   => $request->input('api_game_id'),
                'reroll_count'  => 0,
                'created_at'    => Carbon::now()->toDateTimeString(),
                'updated_at'    => Carbon::now()->toDateTimeString(),
            ]);

            $numberRerolls = 0;
        }
        elseif ($request->input('reroll', false)) 
        {
            if(!$checkCardExists)
                throw new \Dingo\Api\Exception\ResourceException('Card does not exist. Please generate card.');

            $numberRerolls = DB::table('card_rerolls')->select('reroll_count')
                                ->where('user_id', $checkCardExists->user_id)
                                ->where('api_game_id', $checkCardExists->api_game_id)
                                ->get()
                                ->first();

            if($numberRerolls->reroll_count == 3)
                throw new \Dingo\Api\Exception\ResourceException('Maximum rerolls reached.');

            DB::table('card_rerolls')
                ->where('user_id', $checkCardExists->user_id)
                ->where('api_game_id', $checkCardExists->api_game_id)
                ->update([
                    'reroll_count'  => DB::raw('reroll_count + 1'),
                    'updated_at'    => Carbon::now()->toDateTimeString(),
                ]);

            $numberRerolls = $numberRerolls->reroll_count + 1;

            $oldCardId = DB::table('cards')->select('id')
                            ->where('user_id', $checkCardExists->user_id)
                            ->where('api_game_id', $checkCardExists->api_game_id)
                            ->get()
                            ->first();

            DB::table('cards')
                ->where('user_id', $checkCardExists->user_id)
                ->where('api_game_id', $checkCardExists->api_game_id)
                ->delete();

            DB::table('card_details')
                ->where('card_id', $oldCardId->id)
                ->delete();
        }
    		
    	$questions = $this->generateQuestions($request);

    	$card = (object) [];
    	$card->user_id = $this->auth->user()->id;
        $card->reroll_count = $numberRerolls;
    	$card->questions = $questions;

    	$card->champions = DB::table('ddragon_champions')->select(['api_id', 'champion_name', 'image_url'])
    					->get()
    					->toArray();

    	$card->items = DB::table('ddragon_items')->select(['api_id', 'name as item_name', 'image_url'])
    					->get()
    					->toArray();

    	$card->summmoners = DB::table('ddragon_summoners')->select(['api_id', 'name as summoner_name', 'image_url'])
    					->get()
    					->toArray();

    	$cardId = DB::table('cards')->insertGetId([
    		'user_id'			=> $card->user_id,
    		'api_game_id'		=> $request->input('api_game_id'),
    		'details_placed'	=> $request->input('question_count'),
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

    private function generateQuestions($request)
    {
    	$questions = DB::table('questions')->select(['id as question_id', 'slug', 'difficulty','multiplier', 'type', 'description'])
    					->get();

    	$defaultQuestion = $questions->get('1');

    	$questions->forget('1');

    	if($request->input('difficulty', null) != null)
    	{
    		$questions = $questions->where('difficulty', $request->input('difficulty', null));
    	}

    	$questions = $questions->random($request->input('question_count'));

    	$questions->prepend($defaultQuestion);

    	return $questions->toArray();
    }
}
