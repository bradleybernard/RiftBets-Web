<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Scrape\ScrapeController;

use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ServerException;

use DB;
use \Carbon\Carbon;

class PollingController extends ScrapeController
{
    const WORLDS_2016_TOURNAMENT = 9;

    public function poll()
    {
        $select = ['schedule.api_tournament_id', 'schedule.api_match_id', 'matches.id as match_id'];

        $matches = DB::table('schedule')->select($select)
                    ->join('matches', 'matches.api_id_long', '=', 'schedule.api_match_id')
                    ->where('matches.state', 'unresolved')
                    ->whereNotNull('schedule.api_match_id')
                    ->where('schedule.scheduled_time', '<=', new Carbon('2016-10-21 22:00:00'))
                    // ->where('schedule.scheduled_time', '<=', Carbon::now())
                    ->get();
        
        foreach($matches as $match) {

            try {
                $response = $this->client->request('GET', 'v2/highlanderMatchDetails?tournamentId='. $match->api_tournament_id .'&matchId=' . $match->api_match_id);
            } catch (ClientException $e) {
                Log::error($e->getMessage()); continue;
            } catch (ServerException $e) {
                Log::error($e->getMessage()); continue;
            }

            $gameMappings = [];
            $response = json_decode((string)$response->getBody());

            foreach ($response->gameIdMappings as $mapping) 
            {
                $gameMappings[] = [
                    'api_match_id'  => $matchId,
                    'api_game_id'   => $mapping->id,
                    'game_hash'     => $mapping->gameHash
                ];
            }

            DB::table('game_mappings')->insert($gameMappings);

            $select = ['games.'];

            $games = DB::table('matches')->select($select)
                        ->join('games', 'games.api_match_id', '=', 'matches.api_id_long')
                        ->leftJoin('game_mappings', 'games.api_id_long', '=', 'game_mappings.api_game_id')
                        ->where('matches.state', 'unresolved')
                        ->whereNotNull('games.game_id')
                        ->whereNull('game_mappings.api_game_id')
                        ->get();

            dd($games);
        }
    }
}
