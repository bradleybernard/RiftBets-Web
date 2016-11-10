<?php

namespace App\Http\Controllers\Leaderboards;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Redis;
use DB;
use Validator;

class LeaderboardsController extends Controller
{
    const PREFIX = 'lb_';
    protected $redis;

    public function __construct()
    {
        $this->redis = Redis::connection();
    }

    public function setup()
    {
        $now = \Carbon\Carbon::now();

        $rows = [
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

        foreach($rows as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        DB::table('leaderboards')->insert($rows);
    }

    public function populate() 
    {
        $users = range(1, 200);

        $this->redis->pipeline(function ($pipe) use ($users) {
            foreach($users as $userId) {
                $pipe->ZADD('lb_weekly_wins', mt_rand(1, 1000), $userId);
                $pipe->ZADD('lb_monthly_wins', mt_rand(1, 1000), $userId);
                $pipe->ZADD('lb_alltime_wins', mt_rand(1, 1000), $userId);

                $pipe->ZADD('lb_weekly_streak', mt_rand(1, 1000), $userId);
                $pipe->ZADD('lb_monthly_streak', mt_rand(1, 1000), $userId);
                $pipe->ZADD('lb_alltime_streak', mt_rand(1, 1000), $userId);
            }
        });
    }

    public function leaderboards(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leaderboard'   => 'required|exists:leaderboards,stat',
            'start'         => 'required|min:1|',
            'end'           => 'required|min:1|gt_than:start',
        ]);

        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\ResourceException('Invalid request sent.', $validator->errors());
        }

        $board = DB::table('leaderboards')->where('stat', $request->get('leaderboard'))->first();

        $userIds = $this->redis->ZREVRANGE(self::PREFIX . $request->get('leaderboard'), $request->get('start'), $request->get('end'));
      
        if($this->auth->user() && !in_array($this->auth->user()->id, $userIds))
        {
            $userIds[] = $this->auth->user()->id;
        }

        $userColumns = [
            'users.id',
            'users.name',
            // 'users.avatar_id',
            'user_stats.' . $board->stat,
        ];

        $users = DB::table('users')->select($userColumns)
                ->join('user_stats', 'user_stats.user_id', '=', 'users.id')
                ->whereIn('users.id', $userIds)
                ->get();

        $desired = array_flip($userIds);

        $sortedUsers = [];
        $json = [];

        foreach($users as $key => $user)
        {
            $sortedUsers[$desired[$user->id]] = $user;

            if($this->auth->user() && $user->id == $this->auth->user()->id)
            {
                $json['me'] = $user;
            }
        }

        ksort($sortedUsers);

        $json['users'] = $sortedUsers;
        $json['leaderboard'] = $board;

        return $this->response->array($json);
    }
}
