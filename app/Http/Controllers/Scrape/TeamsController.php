<?php

namespace App\Http\Controllers\Scrape;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Scrape\ScrapeController;

use \GuzzleHttp\Exception\ClientException;

class TeamsController extends ScrapeController
{
    public function scrape () 
    {
    	$teams = [['api_id' => 11, 'slug' => 'team-solomid', 'tournament_id' => '3c5fa267-237e-4b16-8e86-20378a47bf1c']];
    	$playerRecords = [];
    	$teamPlayerRecords = [];

        foreach($teams as $team)
        {
         	try 
         	{
                $response = $this->client->request('GET', 'v1/teams?slug='.$team['slug'].'&tournament='.$team['tournament_id']);
            } 
            catch (ClientException $e)
            {
                continue;
            }

            $response = json_decode((string) $response->getBody());
            $players = $response->players;

            foreach($players as $player)
            {
            	$playerRecords[] = [
            		'api_id'			=> $player->id,
            		'slug'				=> $player->slug,
            		'name'				=> $player->name,
            		'first_name'		=> $player->firstName,
            		'last_name'			=> $player->lastName,
            		'role_slug'			=> $player->roleSlug,
            		'photo_url'			=> $player->photoUrl,
            		'hometown'			=> $player->hometown,
            		'api_created_at'	=> $player->createdAt,
            		'api_updated_at'	=> $player->updatedAt,
            		'drupal_id'			=> $this->pry($player, 'foreignIds->drupalId')
            	];
            }
         	
         	foreach($response->teams as $roster)
         	{
         		if($roster->slug == $team['slug'])
         		{
            		foreach($playerRecords as $player)
            		{
            			$teamPlayerRecords[] = [
            				'api_id'			=> $player['api_id'],
            				'api_team_id'		=> $roster->id,
            				'starter'			=> in_array($player['api_id'], $roster->starters)
            			];            		
            		}
            		break;
            	}
        	}
         }



         dd($teamPlayerRecords);

    }
}
