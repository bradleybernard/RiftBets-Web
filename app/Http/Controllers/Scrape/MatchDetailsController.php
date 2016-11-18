<?php

namespace App\Http\Controllers\Scrape;

use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ServerException;

use DB;
use Log;

class MatchDetailsController extends ScrapeController
{
    public function scrape()
    {
    	$tournamentId = '3c5fa267-237e-4b16-8e86-20378a47bf1c';
    	//$matchId = '0dae40a2-fdcb-4539-9c39-376c545438fb';

        $matches = DB::table('matches')->select('api_id_long as match_id')->get();

        foreach ($matches as $match) {

            $gameMappings = [];
            $gameVideos   = [];

        	try {
                $response = $this->client->request('GET', 'v2/highlanderMatchDetails?tournamentId='. $tournamentId .'&matchId=' . $match->match_id);
            } catch (ClientException $e) {
                Log::error($e->getMessage()); return;
            } catch (ServerException $e) {
                Log::error($e->getMessage()); return;
            }

            $response = json_decode((string)$response->getBody());

            foreach ($response->gameIdMappings as $mapping) 
            {
                $gameMappings[] = [
                    'api_match_id'  => $match->match_id,
                    'api_game_id'   => $mapping->id,
                    'game_id'       => DB::table('games')->where('api_id_long', $mapping->id)->pluck('game_id')[0],
                    'game_hash'     => $mapping->gameHash
                ];
            }

            foreach($response->videos as $video) {
                $gameVideos[] = [
                    'api_id'            => $video->id,
                    'api_game_id'       => $video->game,
                    'locale'            => $video->locale,
                    'source'            => $video->source,
                    'api_created_at'    => (new \Carbon\Carbon($video->createdAt)),
                    'created_at'        => \Carbon\Carbon::now(),
                ];
            }

            DB::table('game_videos')->insert($gameVideos);
            DB::table('game_mappings')->insert($gameMappings);
        }
    }
}
