<?php

namespace App\Http\Controllers\Scrape;

use Illuminate\Http\Request;
use App\Http\Controllers\Scrape\ScrapeController;

use \GuzzleHttp\Exception\ClientException;

class LeaguesController extends ScrapeController
{
    public function scrape()
    {
        $range = range(1, 30);
        $leagues = [];

        foreach($range as $index) {
            
            try {
                $response = $this->client->request('GET', 'v1/leagues', ['query' => ['id' => $index]]);
            } catch (ClientException $e) {
                continue;
            }

            $response = json_decode((string) $response->getBody());

            foreach($response->leagues as $league) {
                $leagues[] = [
                    'api_id'            => $league->id,
                    'slug'              => $league->slug,
                    'name'              => $league->name,
                    'region'            => $league->region,
                    'drupal_id'         => $league->drupalId,
                    'logo_url'          => $league->logoUrl,
                    'api_created_at'    => $league->createdAt,
                    'api_updated_at'    => $league->updatedAt,
                    'about'             => $this->pry($league, 'abouts->en_US'),
                    'tournaments'       => implode(',', $league->tournaments)
                ];
            }
        }

        dd($leagues);
    }
}
