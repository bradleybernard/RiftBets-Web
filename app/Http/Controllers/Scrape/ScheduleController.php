<?php

namespace App\Http\Controllers\Scrape;

use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ServerException;

use \Carbon\Carbon;
use DB;

class ScheduleController extends ScrapeController
{
    protected $tables = ['schedule'];

    public function scrape()
    {
        $this->reset();
        
        $leagues = DB::table('leagues')->pluck('api_id');

        foreach($leagues as $leagueId) {

            try {
                $response = $this->client->request('GET', 'v1/scheduleItems?leagueId=' . $leagueId);
            } catch (ClientException $e) {
                continue;
            } catch (ServerException $e) {
                continue;
            }

            $response = json_decode((string) $response->getBody());
            $schedules = [];

            foreach($response->scheduleItems as $item) {
                $schedules[] = [
                    'api_league_id'     => $leagueId,
                    'api_id_long'       => $item->id,
                    'api_tournament_id' => $item->tournament,
                    'api_match_id'      => $this->pry($item, 'match'),
                    'block_label'       => $this->pry($item, 'tags->blockLabel'),
                    'block_prefix'      => $this->pry($item, 'tags->blockPrefix'),
                    'sub_block_label'   => $this->pry($item, 'tags->subBlockLabel'),
                    'sub_block_prefix'  => $this->pry($item, 'tags->subBlockPrefix'),
                    'scheduled_time'    => new Carbon($item->scheduledTime),
                ];
            }

            DB::table('schedule')->insert($schedules);
        }
    }
}
