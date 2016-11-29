<?php

namespace App\Http\Controllers\Scrape;

use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ServerException;

use \Carbon\Carbon;
use DB;
use Log;

class PlayersController extends ScrapeController
{
    protected $tables = ['players', 'team_players'];

    //gather and insert data of each player on each team
    public function scrape() 
    {
        $teams = DB::table('rosters')->join('teams', 'rosters.api_team_id', '=', 'teams.api_id')
                    ->select(['api_id', 'api_tournament_id', 'slug'])
                    ->where('api_tournament_id', '3c5fa267-237e-4b16-8e86-20378a47bf1c')
                    ->get();

        foreach($teams as $team)
        {
            $insert = [];

            try {
                $response = $this->client->request('GET', 'v1/teams?slug=' . $team->slug . '&tournament=' . $team->api_tournament_id);
            } catch (ClientException $e) {
                Log::error($e->getMessage()); continue;
            } catch (ServerException $e) {
                Log::error($e->getMessage()); continue;
            }

            $response = json_decode((string) $response->getBody());

            //insert basic player info
            foreach($response->players as $player)
            {
                $insert['players'][] = [
                    'api_id'            => $player->id,
                    'slug'              => $player->slug,
                    'name'              => $player->name,
                    'first_name'        => $this->clean($this->pry($player, 'firstName')),
                    'last_name'         => $this->clean($this->pry($player, 'lastName')),
                    'role_slug'         => $player->roleSlug,
                    'photo_url'         => $this->pry($player, 'photoUrl'),
                    'hometown'          => $this->pry($player, 'hometown'),
                    'drupal_id'         => $this->pry($player, 'foreignIds->drupalId'),
                    'api_created_at'    => new Carbon($player->createdAt),
                    'api_updated_at'    => new Carbon($player->updatedAt),
                ];
            }
            
            foreach($response->teams as $roster)
            {
                if($roster->slug == $team->slug)
                {
                    foreach($insert['players'] as $player)
                    {
                        $insert['team_players'][] = [
                            'api_team_id'       => $roster->id,
                            'api_player_id'     => $player['api_id'],
                            'is_starter'        => in_array($player['api_id'], $roster->starters),
                        ];                  
                    }

                    break;
                }
            }

            foreach($insert as $table => $records) {
                DB::table($table)->insert($records);
            }
        }
    }
}
