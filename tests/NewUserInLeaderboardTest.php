<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NewUserInLeaderboardTest extends TestCase
{
    use DatabaseMigrations;

    // Make sure new user is in fact in the leaderboard after creation
    public function test_new_user_in_leaderboard()
    {
        $this->resetRedis();

        $redis = Redis::connection();
        $accessToken = 'EAAKzu2L3NZCIBANqtTkEcqUqsOS0HaQAOOiTJaPSU2MlAV2ZBDSvSZCMpy6qAlUXTDKQK3UxKrFm5tZAmHofK2krZArEZBluCFo3lkZCbH437pi4DZBFFUmcHgZBQSmPPMfXZAkrgmPFOhjxZAYS7ro9ggPFIQKZA8PwoUZCwjI0jP3u9UwZDZD';

        $this->json('POST', '/api/auth/facebook', ['facebook_access_token' => $accessToken])
             ->seeJsonStructure([
                'token',
                'user' => [
                    'id', 
                    'facebook_id',
                    'name', 
                    'email', 
                    'credits',
                    'device_token',
                    'created_at',
                    'updated_at',
                ]
        ]);

        $userId = 1;
        $rank = $redis->ZRANK('lb_weekly_wins', $userId);

        $this->assertTrue(($rank == 0));
    }

    // Helper to reset redis database (flushall keys)
    private function resetRedis()
    {
        $deleteStr = '';

        $redis = Redis::connection();
        $leaderboards = $this->leaderboards();

        foreach($leaderboards as $leaderboard)
        {
            $deleteStr .= $leaderboard['redis_key'] . " ";
        }

        $redis->DEL(substr($deleteStr, 0, -1));
    }

    // Return array of leaderboards (usually in DB but don't want to test DB for this)
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
