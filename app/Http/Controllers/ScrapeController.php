<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \GuzzleHttp\Client;

use App\Http\Requests;

class ScrapeController extends Controller
{
    public function test()
    {
		$client = new Client(['base_uri' => 'http://api.lolesports.com/api/v1/']);

		$response = $client->request('GET', 'leagues', ['query' => ['id' => 9]]);

		$body = json_decode((string)$response->getBody());

		// dd(json_decode($body));



		$tournaments = $body->highlanderTournaments;
		foreach ($tournaments as $tournament)
		{
			$rosters = [];
			foreach ($tournament->rosters as $roster) 
			{
				$rosters[$roster->id] = $roster->name;
			}

			foreach ($tournament->brackets as $bracket) 
			{
				foreach ($bracket->matches as $match) 
				{
					$rosterOne = $match->input[0]->roster;
					$rosterTwo = $match->input[1]->roster;


					$nameOne = $rosters[$match->input[0]->roster];
					$nameTwo = $rosters[$match->input[1]->roster];

					$scoreOne = $match->scores->{$rosterOne};
					$scoreTwo = $match->scores->{$rosterTwo};

					echo  $match->name . ' ' . $nameOne . ': ' . $scoreOne . ' ' . $nameTwo . ': '. $scoreTwo . "<br>";
				}
			}
		}

		// Scrape the rosters get a list of id and name (id => key)



		// Scrape and print out match name and what each teams score was

    }
}
