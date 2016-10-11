<?php

namespace App\Http\Controllers\Scrape;

use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ServerException;
use DB;

class DetailsController extends ScrapeController
{
    public function scrape()
    {
    	$tournamentId = '3c5fa267-237e-4b16-8e86-20378a47bf1c';
    	$matchId = '0dae40a2-fdcb-4539-9c39-376c545438fb';

        $gameMappings = [];
        // $matchVideos = [];

    	try
     	{
            $response = $this->client->request('GET', 'v2/highlanderMatchDetails?tournamentId='. $tournamentId .'&matchId=' . $matchId);
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

        foreach ($response->gameIdMappings as $mapping) 
        {
            $gameMappings[] = [
                'api_match_id'  => $matchId,
                'api_game_id'   => $mapping->id,
                'game_hash'     => $mapping->gameHash
            ];
        }

        // foreach ($response->videos as $video)
        // {
        //     $matchVideos = [
        //         'video_id'      => $video->id,
        //         'game_hash'     => $video->gameHash,
        //     ];
        // }

        DB::table('game_mappings')->insert($gameMappings);
    }
}
