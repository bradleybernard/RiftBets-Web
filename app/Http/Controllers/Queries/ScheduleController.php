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
            'block_prefix', 'block_label', 'sub_block_prefix', 'sub_block_label', 'scheduled_time', 'name',
            'state', 'api_resource_id_one', 'api_resource_id_two', 'resource_type', 'score_one', 'score_two',
        ];

        $rows = DB::table('schedule')->select($columns)
            ->orderBy('scheduled_time', 'asc')
            ->leftJoin('matches', 'matches.api_id_long', '=', 'schedule.api_match_id')
            ->where('api_tournament_id', '3c5fa267-237e-4b16-8e86-20378a47bf1c')
            ->get();

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
            return $item;
        });

        $rows = $rows->keyBy('scheduled_time');

        foreach ($rows as $key => $value) {
           $x = date("m/d/Y", strtotime($key));
           $rows[$x] = $rows[$key];
           unset($rows[$key]);
        }

        // dd($rows);
        return response()->json([
        	$rows]);

    }
}
