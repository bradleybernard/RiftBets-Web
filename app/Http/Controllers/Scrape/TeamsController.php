<?php

namespace App\Http\Controllers\Scrape;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Scrape\ScrapeController;

use \GuzzleHttp\Exception\ClientException;

class TeamsController extends ScrapeController
{
    public function scrape () 
    {
    	try {
                $response = $this->client->request('GET', 'v1/teams?slug=team-solomid&tournament=3c5fa267-237e-4b16-8e86-20378a47bf1c', ['query' => ['id' => $index]]);
            } catch (ClientException $e) {
                continue;
            }

    }
}
