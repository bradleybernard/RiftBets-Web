<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\User;
use JWTAuth;
use DB;

class TestController extends Controller
{
    public function generate(Request $request)
    {
        if(!$user = User::where('id', 1)->first()) 
        {
            $user = User::create([
                'facebook_id'   => '1',
                'name'          => 'travis',
                'credit'        => 1,
            ]);
        }

        $token = JWTAuth::fromUser($user);
        
        return $this->response->array([
            'token' => $token,
            'user'  => $user,
        ]);
    }

    public function query()
    {
        $columns = [
            'block_prefix', 'block_label', 'sub_block_prefix', 'sub_block_label', 'scheduled_time', 'name',
            'state', 'api_resource_id_one', 'api_resource_id_two', 'resource_type', 'score_one', 'score_two',
        ];

        $rows = DB::table('schedule')->select($columns)
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

        dd($rows);
    }

    public function authenticate(Request $request)
    {
    	try 
    	{
	    	if (! $user = JWTAuth::parseToken()->authenticate()) 
	    	{
	            return response()->json(['user_not_found'], 404);
	        }
    	} 

    	catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) 
    	{
        	return response()->json(['token_expired'], $e->getStatusCode());
	    } 

	    catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) 
	    {
	        return response()->json(['token_invalid'], $e->getStatusCode());
	    }

	    catch (Tymon\JWTAuth\Exceptions\JWTException $e) 
	    {
	        return response()->json(['token_absent'], $e->getStatusCode());
	    }

	    // the token is valid and we have found the user via the sub claim
	    return response()->json(compact('user'));
    }

    public function test()
    {
    	$user = DB::table('leagues')->where('slug', 'worlds')->first();
    	return $this->response->array((array)$user);
    }
}
