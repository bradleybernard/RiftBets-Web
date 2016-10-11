<?php

namespace App\Http\Controllers\Scrape;

use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ServerException;

use \Carbon\Carbon;
use DB;

class LeaguesController extends ScrapeController
{
    protected $tables = [
        'leagues', 'teams', 'tournaments', 'rosters',
        'breakpoints', 'breakpoint_resources', 'brackets',
        'matches', 'games', 'bracket_resources', 'bracket_records',
    ];

    public function scrape()
    {
        $this->reset();

        $leagues = [9];

        foreach($leagues as $index) {
            
            try {
                $response = $this->client->request('GET', 'v1/leagues', ['query' => ['id' => $index]]);
            } catch (ClientException $e) {
                continue;
            } catch (ServerException $e) {
                continue;
            }

            $response = json_decode((string) $response->getBody());
            $insert = [];

            foreach($response->leagues as $league) {
                $insert['leagues'][] = [
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

            foreach($response->highlanderRecords as $record) {
                $insert['bracket_records'][] = [
                    'api_tournament_id'     => $record->tournament,
                    'api_bracket_id'        => $record->bracket,
                    'api_roster_id'         => $record->roster,
                    'wins'                  => $record->wins,
                    'losses'                => $record->losses,
                    'ties'                  => $record->ties,
                    'score'                 => $record->score,
                ];
            }

            foreach($response->teams as $team) {
                $insert['teams'][] = [
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

            foreach($response->highlanderTournaments as $tournament) {
                
                if($tournament->title != 'world_championship_2016') {
                    continue;
                }

                $insert['tournaments'][] = [
                    'api_league_id'         => $tournament->league,
                    'api_id_long'           => $tournament->id,
                    'title'                 => $tournament->title,
                    'description'           => $tournament->description,
                    'published'             => $tournament->published,
                    'start_date'            => $this->pry($tournament, 'startDate') ? new Carbon($tournament->startDate) : null,
                    'end_date'              => $this->pry($tournament, 'endDate') ? new Carbon($tournament->endDate) : null,
                ];

                foreach($tournament->rosters as $roster) {
                    $insert['rosters'][] = [
                        'api_tournament_id'     => $tournament->id,
                        'api_id_long'           => $roster->id,
                        'api_team_id'           => $this->pry($roster, 'team'),
                        'name'                  => $roster->name,
                    ];
                }

                if($this->pry($tournament, 'breakpoints')) {
                    foreach($tournament->breakpoints as $breakpoint) {
                        $insert['breakpoints'][] = [
                            'api_tournament_id'     => $tournament->id,
                            'api_id_long'           => $breakpoint->id,
                            'name'                  => $breakpoint->name,
                            'position'              => $breakpoint->position,
                            'generator_identifier'  => $breakpoint->generator->identifier
                        ];

                        foreach($breakpoint->input as $breakpointResource) {
                            // Some breakpoint input lists have roster and bracket
                            $insert['breakpoint_resources'][] = [
                                'api_breakpoint_id'     => $breakpoint->id,
                                'api_resource_id'       => $this->pluckResource($breakpointResource),
                                'resource_type'         => $this->pluckResourceType($breakpointResource),
                                'standing'              => $this->pry($breakpointResource, 'standing'),
                            ];
                        }
                    }
                }

                foreach($tournament->brackets as $bracket) {
                    $insert['brackets'][] = [
                        'api_tournament_id'         => $tournament->id,
                        'api_id_long'               => $bracket->id,
                        'name'                      => $this->pry($bracket, 'name'),
                        'position'                  => $bracket->position,
                        'group_position'            => $bracket->groupPosition,
                        'group_name'                => $this->pry($bracket, 'groupName'),
                        'can_manufacture'           => $bracket->canManufacture,
                        'state'                     => $bracket->state,
                        'game_identifier'           => $this->pry($bracket, 'gameMode->identifier'),
                        'game_required_players'     => $this->pry($bracket, 'gameMode->requiredPlayers'),
                        'game_map_name'             => $this->pry($bracket, 'gameMode->mapName'),
                        'game_required_teams'       => $this->pry($bracket, 'gameMode->requiredTeams'),
                        'bracket_identifier'        => $this->pry($bracket, 'bracketType->identifier'),
                        'bracket_rounds'            => $this->pry($bracket, 'bracketType->options->rounds'),
                        'match_identifier'          => $this->pry($bracket, 'matchType->identifier'),
                        'match_best_of'             => $this->pry($bracket, 'matchType->options->best_of'),
                    ];

                    if($this->pry($bracket, 'input')) {
                        foreach($bracket->input as $bracketRoster) {
                            $insert['bracket_resources'][] = [
                                'api_bracket_id'    => $bracket->id,
                                'api_resource_id'   => $this->pluckResource($bracketRoster),
                                'resource_type'     => $this->pluckResourceType($bracketRoster),
                            ];
                        }
                    }

                    foreach($bracket->matches as $match) {

                        $record = [
                            'api_bracket_id'            => $bracket->id,
                            'api_id_long'               => $match->id,
                            'name'                      => $match->name,
                            'position'                  => $match->position,
                            'state'                     => $match->state,
                            'group_position'            => $match->groupPosition,
                            'scoring_identifier'        => $this->pry($match, 'scoring->identifier'),
                            'api_resource_id_one'       => null,
                            'api_resource_id_two'       => null,
                            'resource_type'             => null,
                            'score_one'                 => null,
                            'score_two'                 => null,
                        ];  

                        $keys = ['one', 'two'];
                        $index = 0;

                        foreach($match->input as $roster) {
                            if(property_exists($roster, 'roster')) {
                                $record['api_resource_id_' . $keys[$index]] = $roster->roster;
                                $record['score_' . $keys[$index++]] = $this->pry($match, 'scores->' . $roster->roster);
                                $record['resource_type'] = 'roster';
                            } else if(property_exists($roster, 'breakpoint')) {
                                $record['api_resource_id_' . $keys[$index++]] = $roster->breakpoint;
                                $record['resource_type'] = 'breakpoint';
                            } else if(property_exists($roster, 'match')) {
                                $record['api_resource_id_' . $keys[$index++]] = $roster->match;
                                $record['resource_type'] = 'match';
                            }
                        }

                        $insert['matches'][] = $record;

                        foreach($match->games as $game) {
                            $insert['games'][] = [
                                'api_match_id'      => $match->id,
                                'api_id_long'       => $game->id,
                                'name'              => $game->name,
                                'generated_name'    => $game->generatedName,
                                'game_id'           => $this->pry($game, 'gameId'),
                                'game_realm'        => $this->pry($game, 'gameRealm'),
                                'platform_id'       => $this->pry($game, 'platformId'),
                                'revision'          => $game->revision,
                            ];
                        }

                        foreach($match->remadeGames as $game) {
                            $insert['games'][] = [
                                'api_match_id'      => $match->id,
                                'api_id_long'       => $game->id,
                                'name'              => $game->name,
                                'generated_name'    => $game->generatedName,
                                'game_id'           => $this->pry($game, 'gameId'),
                                'game_realm'        => $this->pry($game, 'gameRealm'),
                                'platform_id'       => $this->pry($game, 'platformId'),
                                'revision'          => $game->revision,
                            ];
                        }
                    }
                }
            }

            foreach($insert as $table => $records) {
                if($table == 'teams') {
                    $this->insertTeams($records);
                } else {
                    DB::table($table)->insert($records);
                }
            }
        }
    }

    private function insertTeams($records) 
    {
        $collection = collect($records);
        $apiIds = $collection->pluck('api_id')->toArray();

        $match = DB::table('teams')->select('api_id')->whereIn('api_id', $apiIds)->pluck('api_id')->toArray();

        $insert = $collection->filter(function ($value, $key) use ($match) {
            return !in_array($value['api_id'], $match);
        });

        DB::table('teams')->insert($insert->toArray());
    }
}
