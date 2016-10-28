<?php

namespace App\Http\Controllers\Queries;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;

class MatchDetailsController extends Controller
{
    public function query()
    {
    	$matchId = '0dae40a2-fdcb-4539-9c39-376c545438fb';

    	$columns = ['matches.api_id_long', 'matches.name', 'resource_type', 'matches.api_resource_id_one', 'matches.api_resource_id_two',
    			 'matches.score_one', 'matches.score_two'];

    	$rows = DB::table('matches')->select($columns)
    			->where('matches.api_id_long', $matchId)
    			->get();

    	$filtered = $rows->filter(function ($value, $key) {
            return $value->resource_type == 'roster';
        });

        $rosters = $filtered->pluck('api_resource_id_one')->push($filtered->pluck('api_resource_id_two')->first());

        $columns = [
            'rosters.api_id_long', 'teams.name', 'teams.team_photo_url', 'teams.logo_url', 
            'teams.acronym', 'teams.alt_logo_url', 'teams.slug'
        ];

        $teams = DB::table('rosters')->select($columns)
            ->join('teams', 'rosters.api_team_id', '=', 'teams.api_id')
            ->whereIn('rosters.api_id_long', $rosters->all())
            ->get()
            ->keyBy('api_id_long');

        $rows->transform(function ($item, $key) use ($teams) {
            $item->resources = [
                'one' => $teams->get($item->api_resource_id_one),
                'two' => $teams->get($item->api_resource_id_two),
            ];
            return $item;
        });

    	$columns = ['games.name as game_name', 'games.game_id', 'games.generated_name', 'game_team_stats.team_id',
    				'game_team_stats.win', 'game_team_stats.first_blood', 'game_team_stats.first_inhibitor',
    				'game_team_stats.first_baron', 'game_team_stats.first_dragon', 'game_team_stats.first_rift_herald',
    				'game_team_stats.tower_kills', 'game_team_stats.inhibitor_kills', 'game_team_stats.baron_kills',
    				'game_team_stats.dragon_kills', 'game_team_stats.rift_herald_kills', 'game_team_stats.ban_1',
    				'game_team_stats.ban_2', 'game_team_stats.ban_3'];

    	$games = DB::table('games')->select($columns)
    			->where('games.api_match_id', $matchId)
    			->orderBy('game_name', 'asc')
    			->join('game_team_stats', 'game_team_stats.game_id', '=', 'games.game_id')
    			->get();

    	$games = $games->filter(function ($value, $key) {
			return $value->game_id !== null;
		});

        $team1 = $games->where('team_id', 100)->keyBy('game_name');
        $team2 = $games->where('team_id', 200)->keyBy('game_name');

        $gameIds = $games->pluck('game_id')->unique();

        $gameNumber = $gameIds->count();

        $teamOnePlayers = DB::table('game_player_stats')
                        ->whereIn('game_id', $gameIds)
                        ->where('team_id', 100)
                        ->get()
                        ->groupBy('game_id');

        $teamTwoPlayers = DB::table('game_player_stats')
                        ->whereIn('game_id', $gameIds)
                        ->where('team_id', 200)
                        ->get()
                        ->groupBy('game_id');

        foreach ($teamOnePlayers as $game)
        {
            foreach ($game as $player) 
            {   
                unset($player->id);
                unset($player->game_id);
                unset($player->profile_icon);
            }
        }

        foreach ($teamTwoPlayers as $game)
        {
            foreach ($game as $player) 
            {   
                unset($player->id);
                unset($player->game_id);
                unset($player->profile_icon);
            }
        }


        $team1->transform(function ($item, $key) use ($teamOnePlayers)
        {
            $item->player_stats = $teamOnePlayers[$item->game_id]->keyBy('participant_id')->all();
            return $item;
        }); 

        $team2->transform(function ($item, $key) use ($teamTwoPlayers)
        {
            $item->players_stats = $teamTwoPlayers[$item->game_id]->keyBy('participant_id')->all();
            return $item;
        });

        $rows->transform(function ($item, $key) use ($team1, $team2) {
            $game_name = $team1->get('G1')->game_name;
            $game_id = $team1->get('G1')->game_id;
            $generated_name = $team1->get('G1')->generated_name;

            unset($team1['G1']->game_name);
            unset($team1['G1']->game_id);
            unset($team1['G1']->generated_name);
            unset($team2['G1']->game_name);
            unset($team2['G1']->game_id);
            unset($team2['G1']->generated_name);

            $item->game_one = [
                'game_name'         => $game_name,
                'game_id'           => $game_id,
                'generated_name'    => $generated_name,
                'team_one'          => $team1->get('G1'),
                'team_two'          => $team2->get('G1'),
            ];
            return $item;
        });

        if ($gameNumber >= 2) {
            $rows->transform(function ($item, $key) use ($team1, $team2) {
                $game_name = $team1->get('G2')->game_name;
                $game_id = $team1->get('G2')->game_id;
                $generated_name = $team1->get('G2')->generated_name;

                unset($team1['G2']->game_name);
                unset($team1['G2']->game_id);
                unset($team1['G2']->generated_name);
                unset($team2['G2']->game_name);
                unset($team2['G2']->game_id);
                unset($team2['G2']->generated_name);

                $item->game_two = [
                    'game_name'         => $game_name,
                    'game_id'           => $game_id,
                    'generated_name'    => $generated_name,
                    'team_one'          => $team1->get('G2'),
                    'team_two'          => $team2->get('G2'),
                ];
                return $item;
            });
        }

        if ($gameNumber >= 3) {
            $rows->transform(function ($item, $key) use ($team1, $team2) {
                $game_name = $team1->get('G3')->game_name;
                $game_id = $team1->get('G3')->game_id;
                $generated_name = $team1->get('G3')->generated_name;

                unset($team1['G3']->game_name);
                unset($team1['G3']->game_id);
                unset($team1['G3']->generated_name);
                unset($team2['G3']->game_name);
                unset($team2['G3']->game_id);
                unset($team2['G3']->generated_name);

                $item->game_three = [
                    'game_name'         => $game_name,
                    'game_id'           => $game_id,
                    'generated_name'    => $generated_name,
                    'team_one'          => $team1->get('G3'),
                    'team_two'          => $team2->get('G3'),
                ];
                return $item;
            });
        }

        if ($gameNumber >= 4) {
            $rows->transform(function ($item, $key) use ($team1, $team2) {
                $game_name = $team1->get('G4')->game_name;
                $game_id = $team1->get('G4')->game_id;
                $generated_name = $team1->get('G4')->generated_name;

                unset($team1['G4']->game_name);
                unset($team1['G4']->game_id);
                unset($team1['G4']->generated_name);
                unset($team2['G4']->game_name);
                unset($team2['G4']->game_id);
                unset($team2['G4']->generated_name);

                $item->game_four = [
                    'game_name'         => $game_name,
                    'game_id'           => $game_id,
                    'generated_name'    => $generated_name,
                    'team_one'          => $team1->get('G4'),
                    'team_two'          => $team2->get('G4'),
                ];
                return $item;
            });  
        }

        if ($gameNumber == 5) {
            $rows->transform(function ($item, $key) use ($team1, $team2) {
                $game_name = $team1->get('G5')->game_name;
                $game_id = $team1->get('G5')->game_id;
                $generated_name = $team1->get('G5')->generated_name;

                unset($team1['G5']->game_name);
                unset($team1['G5']->game_id);
                unset($team1['G5']->generated_name);
                unset($team2['G5']->game_name);
                unset($team2['G5']->game_id);
                unset($team2['G5']->generated_name);

                $item->game_five = [
                    'game_name'         => $game_name,
                    'game_id'           => $game_id,
                    'generated_name'    => $generated_name,
                    'team_one'          => $team1->get('G5'),
                    'team_two'          => $team2->get('G5'),
                ];
                return $item;
            });   
        }

        return $this->response->array($rows);
    }
}
