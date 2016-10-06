<?php

namespace App\Http\Controllers\Scrape;

use Illuminate\Http\Request;
use App\Http\Controllers\Scrape\ScrapeController;

use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ServerException;
use \GuzzleHttp\Client;

use \Carbon\Carbon;
use DB;

class LeaguesController extends ScrapeController
{
    public function scrape()
    {
        $range = range(1, 50);

        foreach($range as $index) {
            
            try {
                $response = $this->client->request('GET', 'v1/leagues', ['query' => ['id' => $index]]);
            } catch (ClientException $e) {
                continue;
            } catch (ServerException $e) {
                continue;
            }

            $response = json_decode((string) $response->getBody());

            $leagues = [];
            $tournaments = [];
            $brackets = [];
            $teams = [];

            foreach($response->leagues as $league) {
                $leagues[] = [
                    'api_id'            => $league->id,
                    'slug'              => $this->pry($league, 'slug'),
                    'name'              => $this->pry($league, 'name'),
                    'region'            => $this->pry($league, 'region'),
                    'drupal_id'         => $this->pry($league, 'drupalId'),
                    'logo_url'          => $this->pry($league, 'logoUrl'),
                    'api_created_at'    => new Carbon($league->createdAt),
                    'api_updated_at'    => new Carbon($league->updatedAt),
                ];
            }

            foreach($response->teams as $team) {
                $teams[] = [
                    'api_id'            => $team->id,
                    'slug'              => $team->slug,
                    'name'              => $team->name,
                    'team_photo_url'    => $this->pry($team, 'teamPhotoUrl'),
                    'logo_url'          => $this->pry($team, 'logoUrl'),
                    'acronym'           => $this->pry($team, 'acronym'),
                    'alt_logo_url'      => $this->pry($team, 'altLogoUrl'),
                    'api_created_at'    => new Carbon($team->createdAt),
                    'api_updated_at'    => new Carbon($team->updatedAt),
                    'drupalId'          => $this->pry($team, 'drupalId'),
                ];
            }

            DB::table('leagues')->insert($leagues);
            DB::table('teams')->insert($teams);

            foreach($response->highlanderTournaments as $tournament) {
                $tournaments[] = [
                    'api_league_id'         => $tournament->league,
                    'api_id_long'           => $tournament->id,
                    'title'                 => $tournament->title,
                    'description'           => $tournament->description,
                    'published'             => $tournament->published,
                    'start_date'            => $this->pry($tournament, 'startDate') ? new Carbon($tournament->startDate) : null,
                    'end_date'              => $this->pry($tournament, 'endDate') ? new Carbon($tournament->endDate) : null,
                ];

                foreach($tournament->brackets as $bracket) {
                    $brackets[] = [
                        'api_tournament_id'     => $tournament->id,
                        'api_id_long'           => $bracket->id,
                        'name'                  => $this->pry($bracket, 'name'),
                        'position'              => $bracket->position,
                        'group_position'        => $bracket->groupPosition,
                        'can_manufacture'       => $bracket->canManufacture,
                        'state'                 => $bracket->state,
                    ];
                }
            }

            DB::table('tournaments')->insert($tournaments);
            DB::table('brackets')->insert($brackets);

        }
    }
}
