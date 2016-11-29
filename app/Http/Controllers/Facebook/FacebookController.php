<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;

use DB;
use App\User;
use JWTAuth;
use Log;
use Redis;

class FacebookController extends Controller
{
    // Login/register a user by Facebook
    public function facebook(Request $request)
    {
    	$fb = app(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk::class);
    	$accessToken = $request['facebook_access_token'];

    	try {
  			$response = $fb->get('/me?fields=id,name,email', $accessToken);
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
            Log::error($e->getMessage());
  			dd($e->getMessage());
		}

		$userNode = $response->getGraphUser();

        if(!$user = User::where('facebook_id', $userNode->getId())->first()) {
            $user = User::create([
                'facebook_id'   => $userNode->getId(),
                'name'          => $userNode->getName(),
                'email'         => $userNode->getEmail(),
                'credits'       => 0,
                'device_token'  => $request->get('device_token'),
            ]);

            DB::table('user_stats')->insert([
                'user_id'       => $user->id,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ]);
        }

        $token = JWTAuth::fromUser($user);

        $redis = Redis::connection();
        $leaderboards = $this->leaderboards();

        foreach($leaderboards as $leaderboard) {
            $redis->ZADD($leaderboard['redis_key'], 0, $user->id);
        }

        return $this->response->array([
            'token' => $token,
            'user'  => $user,
        ]);
    }

    // Helper to insert user into redis database
    private function leaderboards()
    {
        return [
            [
                'title'         => 'Weekly Wins',
                'prize'         => '$100',
                'redis_key'     => 'lb_weekly_wins',
                'stat'          => 'weekly_wins',
                'round'         => 1,
                'timeframe'     => 'weekly',
            ],
            [
                'title'         => 'Monthly Wins',
                'prize'         => '$200',
                'redis_key'     => 'lb_monthly_wins',
                'stat'          => 'monthly_wins',
                'round'         => 1,
                'timeframe'     => 'monthly',
            ],
            [
                'title'         => 'Alltime Wins',
                'prize'         => '$300',
                'redis_key'     => 'lb_alltime_wins',
                'stat'          => 'alltime_wins',
                'round'         => 1,
                'timeframe'     => 'alltime',
            ],
            [
                'title'         => 'Weekly Streak',
                'prize'         => '$100',
                'redis_key'     => 'lb_weekly_streak',
                'stat'          => 'weekly_streak',
                'round'         => 1,
                'timeframe'     => 'weekly',
            ],
            [
                'title'         => 'Monthly Streak',
                'prize'         => '$200',
                'redis_key'     => 'lb_monthly_streak',
                'stat'          => 'monthly_streak',
                'round'         => 1,
                'timeframe'     => 'monthly',
            ],
            [
                'title'         => 'Alltime Streak',
                'prize'         => '$300',
                'redis_key'     => 'lb_alltime_streak',
                'stat'          => 'alltime_streak',
                'round'         => 1,
                'timeframe'     => 'alltime',
            ],
        ];
    }
}
