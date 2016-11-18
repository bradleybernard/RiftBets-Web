<?php

namespace App\Http\Controllers\Queries;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use Validator;
use \Carbon\Carbon;

class MatchDetailsController extends Controller
{
    public function query(Request $request)
    {
    	$matchId = $request['match_id'];

        $validator = Validator::make($request->all(), [
            'match_id' => 'exists:matches,api_id_long'
        ]);

        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\ResourceException('Invalid match id.', $validator->errors());
        }

    	$columns = ['matches.api_id_long', 'matches.name', 'resource_type', 'matches.state','matches.api_resource_id_one', 'matches.api_resource_id_two',
    			 'matches.score_one', 'matches.score_two'];

    	$rows = DB::table('matches')->select($columns)
    			->where('matches.api_id_long', $matchId)
    			->get();

        $bettable = $this->isMatchBettable($request, false);

        $rows->transform(function ($item, $key) use ($bettable) {
            $item->bettable_game = $bettable;
            return $item;
        });

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

        $score1 = $games->where('team_id', 100)->sum('win');
        $score2 = $games->where('team_id', 200)->sum('win');

        $rows->transform(function ($item, $key) use($score1, $score2){
            $item->score_one = $score1;
            $item->score_two = $score2;
            return $item;
        });

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

        $allplayers = DB::table('game_player_stats')
                        ->whereIn('game_id', $gameIds)
                        ->get()
                        ->groupBy('game_id');

        $summoners = DB::table('ddragon_summoners')->select('api_id as spell_id', 'name as spell_name', 'image_url')
                        ->get()
                        ->keyBy('spell_id');

        $champions = DB::table('ddragon_champions')->select('api_id as champion_id', 'champion_name', 'image_url')
                        ->get()
                        ->keyBy('champion_id');

        $itemSlot = ['item_1', 'item_2', 'item_3', 'item_4', 'item_5', 'item_6'];

        $items = collect([]);

        foreach ($allplayers as $game)
        {
            foreach ($game as $player) 
            {
                foreach ($itemSlot as $value) 
                {
                    $items->push($player->{$value});

                }
            }
        }

        $items = $items->unique();

        $items = $items->reject(function ($value, $key) {
            return $value == null;
        });

        $items = $items->flatten()->toArray();

        $allItems = DB::table('ddragon_items')->select(['api_id as item_id', 'name', 'image_url'])
                        ->whereIn('api_id', $items)
                        ->get()
                        ->keyBy('item_id');

        foreach ($teamOnePlayers as $game)
        {
            foreach ($game as $player) 
            {   
                $player->champion = [
                    'champion_id'   => $player->champion_id,
                    'champion_name' => $champions->get($player->champion_id)->champion_name,
                    'image_url'     => $champions->get($player->champion_id)->image_url,
                ];

                $player->spell_1 = [
                    'spell_id'      => $player->spell1_id,
                    'spell_name'    => $summoners->get($player->spell1_id)->spell_name,
                    'image_url'     => $summoners->get($player->spell1_id)->image_url,
                ];

                $player->spell_2 = [
                    'spell_id'      => $player->spell2_id,
                    'spell_name'    => $summoners->get($player->spell2_id)->spell_name,
                    'image_url'     => $summoners->get($player->spell2_id)->image_url,
                ];

                foreach ($itemSlot as $key => $value) 
                {
                    if ($player->{$value})
                    {
                        $player->{$value} = [
                            'item_id'   => $player->{$value},
                            'item_name' => $allItems->get($player->{$value})->name,
                            'image_url' => $allItems->get($player->{$value})->image_url,
                        ];
                    }
                }

                unset($player->id);
                unset($player->game_id);
                unset($player->profile_icon);
                unset($player->spell1_id);
                unset($player->spell2_id);
                unset($player->champion_id);
            }
        }

        foreach ($teamTwoPlayers as $game)
        {
            foreach ($game as $player) 
            {   
                foreach ($itemSlot as $key => $value) 
                {
                    if ($player->{$value})
                    {
                        $player->{$value} = [
                            'item_id'   => $player->{$value},
                            'item_name' => $allItems->get($player->{$value})->name,
                            'image_url' => $allItems->get($player->{$value})->image_url,
                        ];
                    }
                }

                $player->spell_1 = [
                    'spell_id'      => $player->spell1_id,
                    'spell_name'    => $summoners->get($player->spell1_id)->spell_name,
                    'image_url'     => $summoners->get($player->spell1_id)->image_url,
                ];

                $player->spell_2 = [
                    'spell_id'      => $player->spell2_id,
                    'spell_name'    => $summoners->get($player->spell2_id)->spell_name,
                    'image_url'     => $summoners->get($player->spell2_id)->image_url,
                ];

                $player->champion = [
                    'champion_id'   => $player->champion_id,
                    'champion_name' => $champions->get($player->champion_id)->champion_name,
                    'image_url'     => $champions->get($player->champion_id)->image_url,
                ];

                unset($player->id);
                unset($player->game_id);
                unset($player->profile_icon);
                unset($player->spell1_id);
                unset($player->spell2_id);
                unset($player->champion_id);
            }
        }

        $banIndex = ['ban_1', 'ban_2', 'ban_3'];

        $team1->transform(function ($item, $key) use ($teamOnePlayers, $champions, $banIndex)
        {   
            foreach ($banIndex as $index) 
            {
                $item->{$index} = [
                    'champion_id'   => $item->{$index},
                    'champion_name' => $champions->get($item->{$index})->champion_name,
                    'image_url'     => $champions->get($item->{$index})->image_url,
                ];
            }

            $item->player_stats = $teamOnePlayers[$item->game_id]->keyBy('participant_id')->all();
            return $item;
        }); 

        $team2->transform(function ($item, $key) use ($teamTwoPlayers, $champions, $banIndex)
        {
            foreach ($banIndex as $index)
            {
                $item->{$index} = [
                    'champion_id'   => $item->{$index},
                    'champion_name' => $champions->get($item->{$index})->champion_name,
                    'image_url'     => $champions->get($item->{$index})->image_url,
                ];
            }

            $item->players_stats = $teamTwoPlayers[$item->game_id]->keyBy('participant_id')->all();
            return $item;
        });

        $allGames = [
            'G1' => 'game_one',
            'G2' => 'game_two',
            'G3' => 'game_three',
            'G4' => 'game_four',
            'G5' => 'game_five',
        ];

        foreach($allGames as $gameKey => $property) {

            if(!$team1->get($gameKey)) 
            {
                    continue;
            }
            
            $rows->transform(function ($item, $key) use ($team1, $team2, $gameKey, $property) {                
                $game_name = $team1->get($gameKey)->game_name;
                $game_id = $team1->get($gameKey)->game_id;
                $generated_name = $team1->get($gameKey)->generated_name;

                unset($team1[$gameKey]->game_name);
                unset($team1[$gameKey]->game_id);
                unset($team1[$gameKey]->generated_name);
                unset($team2[$gameKey]->game_name);
                unset($team2[$gameKey]->game_id);
                unset($team2[$gameKey]->generated_name);

                $item->{$property} = [
                    'game_name'         => $game_name,
                    'game_id'           => $game_id,
                    'generated_name'    => $generated_name,
                    'team_one'          => $team1->get($gameKey),
                    'team_two'          => $team2->get($gameKey),
                ];
                
                return $item;
            });
        }

        return $this->response->array((array)$rows->first());
    }

    public function isMatchBettable(Request $request, $isRoute = true)
    {
        $matchId = $request['match_id'];

        $validator = Validator::make($request->all(), [
            'match_id' => 'exists:matches,api_id_long'
        ]);

        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\ResourceException('Invalid match id.', $validator->errors());
        }

        $match = DB::table('matches')->select('state')
                    ->where('api_id_long', $matchId)
                    ->get()
                    ->first();

        $nextGame = collect([]);

        $currentTime = Carbon::now();

        if($match->state == 'resolved')
        {
            $nextGame = null;
        } 
        else
        {
            $games = DB::table('games')->select(['name as game_name', 'game_id'])
                        ->where('api_match_id', $matchId)
                        ->get()
                        ->keyBy('game_name');

            $games = $games->filter(function ($value, $key) {
                return $value->game_id !== null;
            });

            if(!$games->first())
            {
                $gameStart = DB::table('schedule')->select('scheduled_time')
                                ->where('api_match_id', $matchId)
                                ->get()
                                ->first();

                $gameStart = Carbon::parse($gameStart->scheduled_time);

                $gameStart->addMinutes(5);

                if($currentTime->diffInMinutes($gameStart, false) < 0)
                {
                    $nextGame = null;
                }
                else
                {
                    $nextGame = collect([
                        'game_name'         => 'G1',
                        'bettable_until'    => $gameStart->toDateTimeString(),
                    ]);
                }
            }
            else
            {
                $lastGame = max($games->keys()->toArray());

                $gameStart = DB::table('game_mappings')->select('created_at')
                                ->where('game_id', $games[$lastGame]->game_id)
                                ->get()
                                ->first();

                $gameStart = Carbon::parse($gameStart->created_at);

                $gameStart->addMinutes(15);

                if($currentTime->diffInMinutes($gameStart, false) < 0)
                {
                    $nextGame = null;
                }
                else
                {
                    $nextGame = collect([
                        'game_name'         => 'G' .(1 + substr($lastGame, 1, 1)),
                        'bettable_until'    => $gameStart->toDateTimeString(),
                    ]);
                }
            }
            
        }

        if($isRoute)
        {
            return $this->response->array($nextGame);
        }
        else
        {
            return $nextGame;
        }
    }
}
