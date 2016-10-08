<?php

namespace App\Http\Controllers\Scrape;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \GuzzleHttp\Client;

class DetailsController extends ScrapeController
{
    public function scrape()
    {
    	$tournamentId = '3c5fa267-237e-4b16-8e86-20378a47bf1c';
    	$matchId = '0dae40a2-fdcb-4539-9c39-376c545438fb';
        $matchDetails = [];

    	try
     	{
            $response = $this->client->request('GET', 'v2/highlanderMatchDetails?tournamentId='.$tournamentId
            	.'&matchId='.$matchId);
        }
        catch (ClientException $e)
        {
            dd($e);
        }

        $response = json_decode((string)$response->getBody());

        // dd($response);

        $teams = $response->teams;
        $players = $response->players;
        $schedule = $response->scheduleItems;
        $videos = $response->videos;

        

        foreach ($teams as $team) 
        {
            dd($team->players);
            $mappings = $teams->players;
            echo "$mappings";
            foreach ($mappings as $mapping) 
            {
                $matchDetails[] = [
                    'api_team_id'       => $team->id,
                    'team_slug'         => $team->slug,
                    'team_name'         => $team->name
                    // 'api_player_id'     => $player->
                ];
            }
            
        }
    }
}
