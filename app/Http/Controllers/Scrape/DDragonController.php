<?php

namespace App\Http\Controllers\Scrape;

use App\Http\Controllers\Scrape\ScrapeController;

use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ServerException;

use DB;
use Log;

class DDragonController extends ScrapeController
{
    protected $baseUri = 'http://ddragon.leagueoflegends.com/cdn/';
    protected $apiVersion = '6.21.1';
    protected $tailUri = '/data/en_US/';

    public function scrape($apiVersion = null)
    {
        if($apiVersion) {
            $this->apiVersion = $apiVersion;
        }

        $this->insertProfileIcons();
        $this->insertChampions();
        $this->insertItems();
        $this->insertSummoners();
    }

    private function url($group, $full)
    {
        return 'http://ddragon.leagueoflegends.com/cdn/' . $this->apiVersion . '/img//' . $group . '/' . $full;
    }

    private function insertProfileIcons()
    {
        $insert = [];

        try {
            $response = $this->client->request('GET', $this->apiVersion . $this->tailUri . 'profileicon.json');
        } catch (ClientException $e) {
            Log::error($e->getMessage()); return;
        } catch (ServerException $e) {
            Log::error($e->getMessage()); return;
        }

        $response = json_decode((string) $response->getBody());

        foreach($response->data as $entry) {
            $insert[] = [
                'api_version'   => $this->apiVersion,
                'api_id'        => $entry->id,
                'image_full'    => $entry->image->full,
                'image_group'   => $entry->image->group,
                'image_url'     => $this->url($entry->image->group, $entry->image->full),
            ];
        }

        DB::table('ddragon_profile_icons')->truncate();
        DB::table('ddragon_profile_icons')->insert($insert);
    }

    private function insertChampions()
    {
        $insert = [];

        try {
            $response = $this->client->request('GET', $this->apiVersion . $this->tailUri . 'champion.json');
        } catch (ClientException $e) {
            Log::error($e->getMessage()); return;
        } catch (ServerException $e) {
            Log::error($e->getMessage()); return;
        }

        $response = json_decode((string) $response->getBody());

        foreach($response->data as $entry) {
            $insert[] = [
                'api_version'       => $entry->version,
                'api_id'            => $entry->key,
                'champion_id'       => $entry->id,
                'champion_name'     => $entry->name,
                'champion_title'    => $entry->title,
                'image_full'        => $entry->image->full,
                'image_group'       => $entry->image->group,
                'image_url'         => $this->url($entry->image->group, $entry->image->full),
            ];
        }

        DB::table('ddragon_champions')->truncate();
        DB::table('ddragon_champions')->insert($insert);
    }

    private function insertItems()
    {
        $insert = [];

        try {
            $response = $this->client->request('GET', $this->apiVersion . $this->tailUri . 'item.json');
        } catch (ClientException $e) {
            Log::error($e->getMessage()); return;
        } catch (ServerException $e) {
            Log::error($e->getMessage()); return;
        }

        $response = json_decode((string) $response->getBody());

        foreach($response->data as $key => $entry) {
            $insert[] = [
                'api_version'   => $this->apiVersion,
                'api_id'        => $key,
                'name'          => $entry->name,
                'image_full'    => $entry->image->full,
                'image_group'   => $entry->image->group,
                'image_url'     => $this->url($entry->image->group, $entry->image->full),
            ];
        }

        DB::table('ddragon_items')->truncate();
        DB::table('ddragon_items')->insert($insert);
    }

    private function insertSummoners()
    {
        $insert = [];

        try {
            $response = $this->client->request('GET', $this->apiVersion . $this->tailUri . 'summoner.json');
        } catch (ClientException $e) {
            Log::error($e->getMessage()); return;
        } catch (ServerException $e) {
            Log::error($e->getMessage()); return;
        }

        $response = json_decode((string) $response->getBody());

        foreach($response->data as $key => $entry) {
            $insert[] = [
                'api_version'   => $this->apiVersion,
                'api_id'        => $entry->key,
                'name'          => $entry->name,
                'key'           => $entry->id,
                'description'   => $entry->description,
                'image_full'    => $entry->image->full,
                'image_group'   => $entry->image->group,
                'image_url'     => $this->url($entry->image->group, $entry->image->full),
            ];
        }

        DB::table('ddragon_summoners')->truncate();
        DB::table('ddragon_summoners')->insert($insert);
    }
}
