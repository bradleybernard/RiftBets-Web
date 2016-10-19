<?php

namespace App\Http\Controllers\Bets;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;

class BetController extends Controller
{
    public function question()
    {
    	DB::table('questions')->insert([
    		[
			'slug' 			=> 'test', 
			'description'	=> 'Who can code the best?',
			'type' 			=> 'choice',
			'multiplier' 	=> '3.0',
			'difficulty'	=> 'pretty easy'
			]
		]);
    }

    public function answer()
    {
    	DB::table('answers')->insert([
    		[
			'question_id' 	=> '1', 
			'game_id'		=> '12',
			'answer' 		=> 'Travis'
			]
		]);
    }
}
