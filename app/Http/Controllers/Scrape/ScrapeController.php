<?php

namespace App\Http\Controllers\Scrape;

use App\Http\Controllers\Controller;

use \GuzzleHttp\Client;
use DB;

class ScrapeController extends Controller
{
    protected $client   = null;
    protected $baseUri  = 'http://api.lolesports.com/api/';
    protected $options  = [];
    
    protected $tables   = [];

    public function __construct()
    {
        $options = array_merge(['base_uri'  => $this->baseUri], $this->options);
        $this->client = new Client($options);
    }

    protected function reset()
    {
        foreach($this->tables as $table) {
            DB::table($table)->truncate();
        }
    }

    protected function clean($input)
    {
        $input = trim($input);
        
        if($input == "" || $input == "--") {
            return null;
        }

        return $input;
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

        return (is_string($object) ? $this->clean($object) : $object);
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
