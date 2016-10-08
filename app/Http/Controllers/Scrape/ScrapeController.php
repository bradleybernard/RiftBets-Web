<?php

namespace App\Http\Controllers\Scrape;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use \GuzzleHttp\Client;

class ScrapeController extends Controller
{
    protected $client;
    protected $baseUri = 'http://api.lolesports.com/api/';
    protected $options = [];

    public function __construct()
    {
        $options = array_merge(['base_uri'  => $this->baseUri], $this->options);
        $this->client = $client = new Client($options);
    }

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

    protected function pluckResource($object)
    {
        $keys = ['roster', 'breakpoint', 'match', 'bracket'];

        foreach($keys as $key) {
            if(property_exists($object, $key)) {
                return $object->{$key};
            }
        }

        return null;
    }

    protected function pluckResourceType($object)
    {
        $keys = ['roster', 'breakpoint', 'match', 'bracket'];

        foreach($keys as $key) {
            if(property_exists($object, $key)) {
                return $key;
            }
        }

        return null;
    }
}
