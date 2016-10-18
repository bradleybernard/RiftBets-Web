<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Scrape\ScrapeController;

use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ServerException;

use DB;
use \Carbon\Carbon;
use Log;

class PollingController extends ScrapeController
{
    public function poll()
    {
        $select = [
            'schedule.api_tournament_id', 'schedule.api_match_id', 
            'matches.id as match_id', 'schedule.api_league_id'
        ];

        $matches = DB::table('schedule')->select($select)
                    ->join('matches', 'matches.api_id_long', '=', 'schedule.api_match_id')
                    ->where('matches.state', 'unresolved')
                    ->whereNotNull('schedule.api_match_id')
                    ->where('schedule.scheduled_time', '<=', new Carbon('2016-10-16 22:00:00'))
                    // ->where('schedule.scheduled_time', '<=', Carbon::now())
                    ->get();

        if(!$matches) {
            return;
        }

        $uniqueLeagueIds = $matches->pluck('api_league_id')->unique();
        $matches = $matches->groupBy('api_league_id');

        foreach($uniqueLeagueIds as $leagueId) {

            try {
                $league = $this->client->request('GET', 'v1/leagues?id=' . $leagueId);
            } catch (ClientException $e) {
                Log::error($e->getMessage()); continue;
            } catch (ServerException $e) {
                Log::error($e->getMessage()); continue;
            }

            $league = json_decode((string)$league->getBody());

            foreach($matches->get($leagueId) as $match) {

                try {
                    $response = $this->client->request('GET', 'v2/highlanderMatchDetails?tournamentId='. $match->api_tournament_id .'&matchId=' . $match->api_match_id);
                } catch (ClientException $e) {
                    Log::error($e->getMessage()); continue;
                } catch (ServerException $e) {
                    Log::error($e->getMessage()); continue;
                }

                $gameMappings = [];
                $gameRealm = $this->findGameRealm($league, $match->api_tournament_id);
                $response = json_decode((string)$response->getBody());

                foreach ($response->gameIdMappings as $mapping) 
                {
                    $gameMappings[] = [
                        'api_match_id'  => $match->api_match_id,
                        'api_game_id'   => $mapping->id,
                        'game_id'       => $this->findGameId($league, $mapping->id),
                        'game_hash'     => $mapping->gameHash
                    ];
                }

                if(!$games = $this->insertUniqueGameMappings($gameMappings, $gameRealm)) {
                    continue;
                }

                $this->scrapeGamesDetails($games);
                $this->scrapeGamesTimeslines($games);
            }
        }
    }

    protected function scrapeGamesTimeslines($games)
    {
        $playerStats = [];
        $gameEvents = [];
        $eventDetails = [];

        $client = new Client(['base_uri'  => 'https://acs.leagueoflegends.com/']);

        foreach($games as $game) {

            try {
                $response = $client->request('GET', 'v1/stats/game/' . $game['game_realm'] . '/' . $game['game_id'] . '/timeline?gameHash=' . $game['game_hash']);
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
                        'api_game_id_long'      => $gameHash,
                        'api_game_id'           => $gameId,
                        'api_match_player_id'   => $player->participantId,
                        'x_position'            => $player->position->x,
                        'y_position'            => $player->position->y,
                        'current_gold'          => $player->currentGold,
                        'total_gold'            => $player->totalGold,
                        'level'                 => $player->level,
                        'xp'                    => $player->xp,
                        'minions_killed'        => $player->minionsKilled,
                        'jungle_minions_killed' => $player->jungleMinionsKilled,
                        'dominion_score'        => $player->dominionScore,
                        'team_score'            => $player->teamScore,
                        'game_time_stamp'       => $frame->timestamp
                    ];
                }
                
                $skip = ['type', 'timestamp'];

                foreach ($frame->events as $event) 
                {
                    $gameEvents[] = [
                        'api_game_id'           => $gameId,
                        'game_hash'             => $gameHash,
                        'type'                  => strtolower($event->type),
                        'timestamp'             => $event->timestamp,
                        'unique_id'             => ($gameId . ++$gameEventCounter)
                    ];

                    foreach ($event as $eventKey => $eventValue) 
                    {
                        if(in_array($eventKey, $skip)) {
                            continue;
                        }

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
    }

    protected function scrapeGamesDetails($games)
    {
        $gameStats = [];
        $teamStats = [];
        $playerStats = [];

        $client = new Client(['base_uri'  => 'https://acs.leagueoflegends.com/']);

        foreach($games as $game) {

            try {
                $response = $client->request('GET', 'v1/stats/game/' . $game['game_realm'] . '/' . $game['game_id'] . '?gameHash=' . $game['game_hash']);
            } catch (ClientException $e) {
                Log::error($e->getMessage()); continue;
            } catch (ServerException $e) {
                Log::error($e->getMessage()); continue;
            }

            $response = json_decode((string)$response->getBody());

            $gameStats[] = [
                'game_id'       =>  $response->gameId,
                'platform_id'   =>  $response->platformId,
                'game_creation' =>  $response->gameCreation,
                'game_duration' =>  $response->gameDuration,
                'queue_id'      =>  $response->queueId,
                'map_id'        =>  $response->mapId,
                'season_id'     =>  $response->seasonId,
                'game_version'  =>  $response->gameVersion,
                'game_mode'     =>  $response->gameMode,
                'game_type'     =>  $response->gameType
            ];

            foreach ($response->teams as $team) 
            {
                $teamStats[] = [
                    'game_id'               => $response->gameId,
                    'team_id'               => $team->teamId,
                    'win'                   => $this->parseWin($team->win),
                    'first_blood'           => $team->firstBlood,
                    'first_tower'           => $team->firstTower,
                    'first_inhibitor'       => $team->firstInhibitor,
                    'first_baron'           => $team->firstBaron,
                    'first_dragon'          => $team->firstDragon,
                    'first_rift_herald'     => $team->firstRiftHerald,
                    'tower_kills'           => $team->towerKills,
                    'inhibitor_kills'       => $team->inhibitorKills,
                    'baron_kills'           => $team->baronKills,
                    'dragon_kills'          => $team->dragonKills,
                    'vilemaw_kills'         => $team->vilemawKills,
                    'rift_herald_kills'     => $team->riftHeraldKills,
                    'dominion_victory_score'=> $team->dominionVictoryScore,
                    'ban_1'                 => $team->bans[0]->championId,
                    'ban_1_pick'            => $team->bans[0]->pickTurn,
                    'ban_2'                 => $team->bans[1]->championId,
                    'ban_2_pick'            => $team->bans[1]->pickTurn,
                    'ban_3'                 => $team->bans[2]->championId,
                    'ban_3_pick'            => $team->bans[2]->pickTurn
                ];
            }

            $index = 0;

            foreach ($response->participants as $player) 
            {
                $playerStats[] = [
                    'game_id'               => $response->gameId,
                    'participant_id'        => $player->participantId,
                    'team_id'               => $player->teamId,
                    'champion_id'           => $player->championId,
                    'spell1_id'             => $player->spell1Id,
                    'spell2_id'             => $player->spell2Id,
                    'item_1'                => $this->cleanItem($player->stats->item0),
                    'item_2'                => $this->cleanItem($player->stats->item1),
                    'item_3'                => $this->cleanItem($player->stats->item2),
                    'item_4'                => $this->cleanItem($player->stats->item3),
                    'item_5'                => $this->cleanItem($player->stats->item4),
                    'item_6'                => $this->cleanItem($player->stats->item5),
                    'kills'                 => $player->stats->kills,
                    'deaths'                => $player->stats->deaths,
                    'assists'               => $player->stats->assists,
                    'gold_earned'           => $player->stats->goldEarned,
                    'minions_killed'        => $player->stats->totalMinionsKilled,
                    'champ_level'           => $player->stats->champLevel,
                    'summoner_name'         => $response->participantIdentities[$index]->player->summonerName,
                    'profile_icon'          => $response->participantIdentities[$index++]->player->profileIcon
                ];
            }
        }

        DB::table('game_stats')->insert($gameStats);
        DB::table('game_team_stats')->insert($teamStats);
        DB::table('game_player_stats')->insert($playerStats);
    }

    private function findGameId($league, $gameApiId)
    {
        foreach($league->highlanderTournaments as $tournament) {
            foreach($tournament->brackets as $bracket) {
                foreach($bracket->matches as $match) {
                    foreach($match->games as $game) {
                        if($game->id == $gameApiId) {
                            return $game->gameId;
                        }
                    }
                }
            }
        }
    }

    private function findGameRealm($league, $tournamentId)
    {
        foreach($league->highlanderTournaments as $tournament) {
            if($tournament->id != $tournamentId) {
                continue;
            }
            foreach($tournament->brackets as $bracket) {
                foreach($bracket->matches as $match) {
                    foreach($match->games as $game) {
                        if($realm = $this->pry($game, 'gameRealm')) {
                            return $realm;
                        }
                    }
                }
            }
        }
    }

    private function cleanItem($itemId) 
    {
        return ($itemId == 0 ? null : $itemId);
    }

    private function parseWin($win)
    {
        return ($win == 'Fail' ? false : true);
    }

    private function insertUniqueGameMappings($collection, $realm) 
    {
        $collection = collect($collection);

        if($collection->count() == 0) {
            return;
        }

        $gameHashes = $collection->pluck('game_id')->toArray();

        $match = DB::table('game_mappings')->select('game_id')->whereIn('game_id', $gameHashes)->pluck('game_id')->toArray();

        $insert = $collection->filter(function ($item, $key) use ($match) {
            return !in_array($item['game_id'], $match);
        });

        DB::table('game_mappings')->insert($insert->toArray());

        if($insert->count() == 0) {
            return null;
        }

        $insert->transform(function ($item, $key) use ($realm) {
            $item['game_realm'] = $realm;
            return $item;
        });

        return $insert;
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
                'event_id'  => '1',
                'key'       => $prefix . (is_numeric($key) ? null : '_' . strtolower(snake_case($key))),
                'value'     => strtolower($value),
            ];
        } else {
            // Since we always want to return a collection of rows we must wrap a single record
            // in a containing array hence [[]]
            return [[
                'event_id'  => '1',
                'key'       => strtolower(snake_case($key)),
                'value'     => strtolower($value),
            ]];
        }
    }
}

/*
// $select = ['games.'];
// $games = DB::table('matches')->select($select)
//             ->join('games', 'games.api_match_id', '=', 'matches.api_id_long')
//             ->leftJoin('game_mappings', 'games.api_id_long', '=', 'game_mappings.api_game_id')
//             ->where('matches.state', 'unresolved')
//             ->whereNotNull('games.game_id')
//             ->whereNull('game_mappings.api_game_id')
//             ->get();

// dd($games);
*/