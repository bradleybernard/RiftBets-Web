<?php

namespace App\Http\Controllers\Scrape;

use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ServerException;

use DB;
use Log;

class GameStatsController extends ScrapeController
{
	protected $baseUri = 'https://acs.leagueoflegends.com/';

	public function scrape()
	{
		$gameRealm = 'TRLH1';
		//$gameId = '1001890201';
		//$gameHash = '6751c4ef7ef58654';

		$games = DB::table('game_mappings')->select(['game_mappings.game_id', 'game_mappings.game_hash', 'games.name as game_name'])
					->join('games', 'games.game_id', '=', 'game_mappings.game_id') 
					->get();

		foreach ($games as $game)
		{
			$gameId = $game->game_id;
			$gameHash = $game->game_hash;

			$gameStats = [];
			$teamStats = [];
			$playerStats = [];

			try {
	    		$response = $this->client->request('GET', 'v1/stats/game/' . $gameRealm . '/' . $gameId . '?gameHash=' . $gameHash);
		    } catch (ClientException $e) {
			    Log::error($e->getMessage()); return;
		    } catch (ServerException $e) {
		        Log::error($e->getMessage()); return;
		    }

		    $response = json_decode((string)$response->getBody());

		    $gameStats = [
		    	'game_id'		=>  $response->gameId,
		    	'platform_id'	=>	$response->platformId,
		    	'game_creation'	=>	$response->gameCreation,
		    	'game_duration'	=>	$response->gameDuration,
		    	'queue_id'		=>	$response->queueId,
		    	'map_id'		=>	$response->mapId,
		    	'season_id'		=>	$response->seasonId,
		    	'game_version'	=>	$response->gameVersion,
		    	'game_mode'		=>	$response->gameMode,
		    	'game_type'		=>	$response->gameType
		    ];

		    $swapSides = substr($game->game_name, 1, 1) % 2 == 0;

		    foreach ($response->teams as $team) 
		    {
		    	$teamStats[] = [
		    		'game_id'				=> $gameId,
			    	'team_id'				=> $this->fixSide($swapSides, $team->teamId),
			    	'win'					=> $this->parseWin($team->win),
			    	'first_blood'			=> $team->firstBlood,
			    	'first_tower'			=> $team->firstTower,
			    	'first_inhibitor'		=> $team->firstInhibitor,
			    	'first_baron'			=> $team->firstBaron,
			    	'first_dragon'			=> $team->firstDragon,
			    	'first_rift_herald'		=> $team->firstRiftHerald,
			    	'tower_kills'			=> $team->towerKills,
			    	'inhibitor_kills'		=> $team->inhibitorKills,
			    	'baron_kills'			=> $team->baronKills,
			    	'dragon_kills'			=> $team->dragonKills,
			    	'vilemaw_kills'			=> $team->vilemawKills,
			    	'rift_herald_kills'		=> $team->riftHeraldKills,
			    	'dominion_victory_score'=> $team->dominionVictoryScore,
			    	'ban_1'					=> $team->bans[0]->championId,
			    	'ban_1_pick'			=> $team->bans[0]->pickTurn,
			    	'ban_2'					=> $team->bans[1]->championId,
			    	'ban_2_pick'			=> $team->bans[1]->pickTurn,
			    	'ban_3'					=> $team->bans[2]->championId,
			    	'ban_3_pick'			=> $team->bans[2]->pickTurn
		    	];
		    }

		    $index = 0;

		    foreach ($response->participants as $player) 
		    {
		    	$playerStats[] = [
		    		'game_id'				=> $gameId,	
			    	'participant_id'		=> $this->fixParticipant($swapSides, $player->teamId, $player->participantId),
			    	'team_id'				=> $this->fixSide($swapSides, $player->teamId),
			    	'champion_id'			=> $player->championId,
			    	'spell1_id'				=> $player->spell1Id,
			    	'spell2_id'				=> $player->spell2Id,
			    	'item_1'				=> $this->cleanItem($player->stats->item0),
			    	'item_2'				=> $this->cleanItem($player->stats->item1),
			    	'item_3'				=> $this->cleanItem($player->stats->item2),
			    	'item_4'				=> $this->cleanItem($player->stats->item3),
			    	'item_5'				=> $this->cleanItem($player->stats->item4),
			    	'item_6'				=> $this->cleanItem($player->stats->item5),
			    	'kills'					=> $player->stats->kills,
			    	'deaths'				=> $player->stats->deaths,
			    	'assists'				=> $player->stats->assists,
			    	'gold_earned'			=> $player->stats->goldEarned,
			    	'minions_killed'		=> $player->stats->totalMinionsKilled,
			    	'champ_level'			=> $player->stats->champLevel,
			    	'summoner_name'			=> $response->participantIdentities[$index]->player->summonerName,
			    	'profile_icon'			=> $response->participantIdentities[$index++]->player->profileIcon
		    	];
		    }

		    DB::table('game_stats')->insert($gameStats);
		    DB::table('game_team_stats')->insert($teamStats);
		    DB::table('game_player_stats')->insert($playerStats);
		}
	}


	private function fixApiIds()
	{
		$columns = [
			'matches.api_id_long', 'matches.api_resource_id_one', 'matches.api_resource_id_two',
			'games.game_id'
		];

		$matches = DB::table('matches')->select($columns)
						->join('games', 'games.api_match_id', '=', 'matches.api_id_long')
						->where('games.name', 'G1')
						->get()
						->keyBy('api_id_long');

		foreach($matches as $match)
		{
			$game = DB::table('game_player_stats')->select('summoner_name')
						->where('game_id', $match->game_id)
						->where('participant_id', '1')
						->get()
						->first();

			$game = substr($game->summoner_name, 0, 3);

			$name_one = DB::table('rosters')->select('rosters.name as team_name')
							->where('api_id_long', $match->api_resource_id_one)
							->get()
							->first();

			if (strlen($name_one->team_name) < 3)
			{
				$name_one->team_name = $name_one->team_name .' ';
			}

			if($game == $name_one->team_name)
			{
				
			} else
			{
				dd($match);
			}
		}
	}

	private function cleanItem($itemId) 
	{
		return ($itemId == 0 ? null : $itemId);
	}

	private function parseWin($win)
	{
		return ($win == 'Fail' ? false : true);
	}

	private function fixSide($swapSides, $teamId)
	{
		if($swapSides)
		{
			if ($teamId == 100)
			{
				return 200;
			} 
			else
			{
				return 100;
			}
		}

		return $teamId;
	}

	private function fixParticipant($swapSides, $teamId, $participant)
	{
		if($swapSides)
		{
			if ($teamId == 100)
			{
				return $participant + 5;
			}
			else
			{
				return $participant - 5;
			}
		}

		return $participant;
	}
}
