<?php

namespace App\Http\Controllers\Queries;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class ScheduleController extends Controller
{
    public function query()
    {
        $columns = [
            'block_prefix', 'block_label', 'sub_block_prefix', 'sub_block_label', 'scheduled_time', 'matches.name',
            'matches.state', 'api_resource_id_one', 'api_resource_id_two', 'resource_type', 'score_one', 'score_two',
            'brackets.name as bracket_name', 'brackets.bracket_identifier', 'brackets.bracket_rounds',
            'brackets.match_identifier', 'brackets.match_best_of', 'matches.api_id_long'
        ];

        $rows = DB::table('schedule')->select($columns)
            ->orderBy('scheduled_time', 'asc')
            ->leftJoin('matches', 'matches.api_id_long', '=', 'schedule.api_match_id')
            ->join('brackets', 'brackets.api_id_long', '=', 'matches.api_bracket_id')
            ->where('schedule.api_tournament_id', '3c5fa267-237e-4b16-8e86-20378a47bf1c')
            ->get();

        $bestof = $rows->filter(function ($value, $key) {
            return $value->score_one === null && $value->match_best_of == 5; 
        });

        $bestmatches = $bestof->pluck('api_id_long');

        $gameTeamStats = DB::table('games')
                ->whereIn('games.api_match_id', $bestmatches->all())
                ->join('game_team_stats', 'game_team_stats.game_id', '=', 'games.game_id')
                ->get()
                ->groupBy('api_match_id');

        foreach ($gameTeamStats as $matchKey => $gameTeamStat)
        {
            $score1 = $gameTeamStat->where('team_id', 100)->sum('win');
            $score2 = $gameTeamStat->where('team_id', 200)->sum('win');

            $rows->transform(function ($item, $key) use($matchKey, $score1, $score2){
                if($item->api_id_long == $matchKey)
                {
                    $item->score_one = $score1;
                    $item->score_two = $score2;
                }
                return $item;
            });
        }

        $filtered = $rows->filter(function ($value, $key) {
            return $value->resource_type == 'roster';
        });

        $rosters = $filtered->pluck('api_resource_id_one')->union($filtered->pluck('api_resource_id_two'))->unique();

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
            $item->scheduled_date = date("Y-m-d", strtotime($item->scheduled_time));
            return $item;
        });



        $rows = $rows->groupBy('scheduled_date');

        return $this->response->array($rows);

    }
}
