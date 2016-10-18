<?php

namespace App\Http\Controllers\Scrape;

use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ServerException;

use DB;
use Log;

class TimelineController extends ScrapeController
{
    protected $baseUri = 'https://acs.leagueoflegends.com/';

	public function scrape()
	{
        $gameRealm = 'TRLH1';
		$gameId = '1001890201';
		$gameHash = '6751c4ef7ef58654';

    	$playerStats = [];
    	$gameEvents = [];
    	$eventDetails = [];
	            
	    try {
	    	$response = $this->client->request('GET', 'v1/stats/game/' . $gameRealm . '/' . $gameId . '/timeline?gameHash=' . $gameHash);
	    } catch (ClientException $e) {
		    Log::error($e->getMessage()); return;
	    } catch (ServerException $e) {
	        Log::error($e->getMessage()); return;
	    }

	    $response = json_decode((string)$response->getBody());
        $gameEventCounter = 0;

	    foreach ($response->frames as $frame) 
	    {
	    	foreach ($frame->participantFrames as $player) 
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
            		'game_time_stamp'		=> $frame->timestamp
            	];
	    	}
	    	
            $skip = ['type', 'timestamp'];

	    	foreach ($frame->events as $event) 
	    	{
	    		$gameEvents[] = [
                    'api_game_id'           => $gameId,
	    			'game_hash'		        => $gameHash,
            		'type'			        => strtolower($event->type),
            		'timestamp'		        => $event->timestamp,
                    'unique_id'             => ($gameId . ++$gameEventCounter),
	    		];

	    		foreach ($event as $eventKey => $eventValue) 
	    		{
	    			if(in_array($eventKey, $skip)) {
                        continue;
                    }

                    // Gather collection of [game_event_details]. There is either
                    // a single record in the collection or multiple records.
                    // Either way we loop thru the collection and append each
                    // record to the eventDetails array
                    $records = $this->collectDetails($eventKey, $eventValue);
                    foreach($records as $record) {
                        $record['event_unique_id'] = ($gameId . $gameEventCounter);
                        $eventDetails[] = $record;
                    }
                }
	    	}
	    }

    	DB::table('per_frame_player_stats')->insert($playerStats);
		DB::table('game_events')->insert($gameEvents);
		DB::table('game_event_details')->insert($eventDetails);
	}

    // Ex: $this->collectDetails('participantId', 6);
    //     ==> [['event_id' => '1', 'key' => 'participant_id', 'value' => '6']]
    // Ex: $this->collectDetails('position', {'x' => 134866, 'y' => 4505});
    //     $this->collectDetails('x', 134866, 'position') and $this->collectDetails('y', 4505, 'position')
    //     ==> [
    //          ['event_id' => '1', 'key' => 'position_x', 'value' => 134866],
    //          ['event_id' => '1', 'key' => 'position_y', 'value' => 4505]    
    //     ]
    private function collectDetails($key, $value, $prefix = null)
    {
        // Loop through all properties/array values if its an array/obj 
        // and append each of those properties to a collection ($return) and
        // return that collection of [game_event_details].
        if(is_array($value) || is_object($value)) {
            $return = [];
            foreach($value as $nestedKey => $nestedValue) {
                $return[] = $this->collectDetails($nestedKey, $nestedValue, strtolower(snake_case($key)));
            }
            return $return;
        } 

        // Not an array/obj so we will just get its key and value.
        // If it has a prefix, it means it came here from the above loop
        // so we must also check if the current key is numeric because an array 
        // has numeric keys so we dont want _0, _1 on our key names so we will just remove 
        // those keys and keep the prefix. 
        // Ex: assisting_participant_ids_0, assisting_participant_ids_1 --> assisting_participant_ids (with two rows)
        if($prefix) {
            return [
                'key'       => $prefix . (is_numeric($key) ? null : '_' . strtolower(snake_case($key))),
                'value'     => strtolower($value),
            ];
        } else {
            // Since we always want to return a collection of rows we must wrap a single record
            // in a containing array hence [[]]
            return [[
                'key'       => strtolower(snake_case($key)),
                'value'     => strtolower($value),
            ]];
        }
    }
}
