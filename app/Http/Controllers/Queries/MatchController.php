<?php

namespace App\Http\Controllers\Queries;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class MatchController extends Controller
{
    public function query()
    {
    	$rows = DB::table('matches')
    		->join('games', 'games.api_match_id', '=', 'matches.api_id_long')
    		->select('game_player_stats.*')
    		->join('game_player_stats', 'game_player_stats.game_id', '=', 'games.game_id')
    		->get();
    		//->keyBy('api_id_long');

    	$matches = DB::table('game_team_stats')
    		->where('game_id', $rows[0]->game_id)
    		->get();


    	$events = DB::table('game_events')
    		->where('api_game_id', $rows[0]->game_id)
    		->join('game_event_details', 'game_event_details.event_id', "=", 'game_events.id')
    		->get();

    	dd($events[0]);
    }
}
