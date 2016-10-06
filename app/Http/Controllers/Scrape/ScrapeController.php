<?php

namespace App\Http\Controllers\Scrape;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use \GuzzleHttp\Client;

class ScrapeController extends Controller
{
    protected $client;

    protected function pry($object, $path)
    {
        $parts = explode('->', $path);

        foreach($parts as $part) {
            if(property_exists($object, $part)) {
                $object = $object->{$part};
            } else {
                return null;
            }
        }

        return $object;
    }

    public function __construct()
    {
        $this->client = $client = new Client([
            'base_uri' => 'http://api.lolesports.com/api/',
        ]);
    }
}
