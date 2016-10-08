<?php

namespace App\Http\Controllers\Scrape;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use \GuzzleHttp\Client;

use \Carbon\Carbon;
use DB;

class StatsController extends ScrapeController
{
	public function scrape()
	{

		$client = new Client([
		    // Base URI for the timelines, needs {gameHash}
		    'base_uri' => 'https://acs.leagueoflegends.com/v1/stats/game/TRLH1/',
		]);

		$gameId = '1001890201';
		$gameHash = '6751c4ef7ef58654';

    	$playerStats = [];
    	$gameEvents = [];
	            
	    try 
	    {
	    	$response = $client->request('GET', $gameId.'/timeline?gameHash='.$gameHash);
	    }
	    catch (ClientException $e)
	    {
		    dd($e);
	    } 
	    catch (ServerException $e) 
	    {
	        dd($e);
	    }

	    $response = json_decode((string)$response->getBody());

	    dd($response->frames[37]);

	    $frames = $response->frames;

	    foreach ($frames as $frame) 
	    {
	    	$players = $frame->participantFrames;
	    	$timeStamp = $frame->timestamp;
	    	$events = $frame->events;

	    	foreach ($players as $player) 
	    	{
	    		$playerStats[] = [
	    			'api_game_id_long'		=> $gameHash,
	    			'api_game_id'			=> $gameId,
            		'api_match_player_id'	=> $player->participantId,
            		'x_position'			=> $player->position->x,
            		'y_position'			=> $player->position->y,
            		'current_gold'			=> $player->currentGold,
            		'total_gold'			=> $player->totalGold,
            		'level'					=> $player->level,
            		'xp'					=> $player->xp,
            		'minions_killed'		=> $player->minionsKilled,
            		'jungle_minions_killed'	=> $player->jungleMinionsKilled,
            		'dominion_score'		=> $player->dominionScore,
            		'team_score'			=> $player->teamScore,
            		'game_time_stamp'		=> $timeStamp
            	];
	    	}
	    	
	    	foreach ($events as $event) 
	    	{
	    		$gameEvents[] = [
	    			'api_game_id_long'		=> $gameHash,
	    			'api_game_id'			=> $gameId,
	    			'api_match_player_id'	=> $this->pry($event, 'participantId'),
            		'event_type'			=> $event->type,
            		'game_time_stamp'		=> $event->timestamp,
            		'level_up_type'			=> $this->pry($event, 'levelUpType'),
            		'ward_type'				=> $this->pry($event, 'wardType'),
            		'killed_id'				=> $this->pry($event, 'killerId'),
            		'creator_id'			=> $this->pry($event, 'creatorId'),
            		'x_position'			=> $this->pry($event, 'position->x'),
            		'y_position'			=> $this->pry($event, 'position->y'),
            		'team_id'				=> $this->pry($event, 'teamId'),
            		'building_type'			=> $this->pry($event, 'buildingType'),
            		'lane_type'				=> $this->pry($event, 'laneType'),
            		'tower_type'			=> $this->pry($event, 'towerType'),
            		'victim_id'				=> $this->pry($event, 'victimId'),
            		'assisting_player_id'	=> $this->pry($event, 'assistingPlayerId'),
            		'monster_type'			=> $this->pry($event, 'monsterType')
	    		];
	    	}
	    }
	    // dd($gameEvents);
    	DB::table('player_stats')->insert($playerStats);
		DB::table('events')->insert($gameEvents);
	}

}
