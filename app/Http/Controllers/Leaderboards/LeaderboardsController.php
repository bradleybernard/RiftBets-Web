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

    public function around(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leaderboard'   => 'required|exists:leaderboards,stat',
            'user_id'       => 'required|exists:users,id',
            'count'         => 'required|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\ResourceException('Invalid request sent.', $validator->errors());
        }

        $board = DB::table('leaderboards')->where('stat', $request->get('leaderboard'))->first();

        $rank = $this->redis->ZREVRANK(self::PREFIX . $request->get('leaderboard'), $request->get('user_id'));

        if($rank === null) {
            return $this->response->errorInternal('Rank not found for this user.');
        }

        $userIds = [];

        if($rank < ($request->get('count') - 1)) {
            $userIds = $this->redis->ZREVRANGE(
                self::PREFIX . $request->get('leaderboard'), 
                0, 
                $request->get('count')
                // ($rank + $request->get('count'))
            );
        } else {
            $userIds = $this->redis->ZREVRANGE(
                self::PREFIX . $request->get('leaderboard'), 
                ($rank - ($request->get('count') / 2)), 
                ($rank + ($request->get('count') / 2))
            );
        }

        if(!in_array($request->get('user_id'), $userIds)) {
            $userIds[] = $request->get('user_id');
        }

        $users = DB::table('users')
                ->select(['users.id', 'users.name', 'user_stats.' . $board->stat  . ' as stat', 'users.facebook_id'])
                ->join('user_stats', 'user_stats.user_id', '=', 'users.id')
                ->whereIn('users.id', $userIds)
                ->get();

        $desired = array_flip($userIds);
        $sortedUsers = $response = [];

        foreach($users as $key => $user) {
            $sortedUsers[$desired[$user->id]] = $user;
            if($this->auth->user() && $user->id == $this->auth->user()->id) {
                $response['me'] = $user;
            }
        }
      
        ksort($sortedUsers);
      
        // $response['first'] = $this->rank($leaderboard, $sortedUsers[0]->id);
        // ^^ why get first user?
        $response['users'] = $sortedUsers;
        $response['leaderboard'] = $board;

        return $this->response->array($response);
    }

    public function rank(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leaderboard'   => 'required|exists:leaderboards,stat',
            'user_id'       => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\ResourceException('Invalid request sent.', $validator->errors());
        }

        $score = $this->redis->ZSCORE(self::PREFIX . $request->get('leaderboard'), $request->get('user_id'));
        $member = $this->redis->ZREVRANGEBYSCORE(self::PREFIX . $request->get('leaderboard'), $score, $score, 'LIMIT', 0, 1);
        $rank = $this->redis->ZREVRANK(self::PREFIX . $request->get('leaderboard'), $member[0]) + 1;

        $stats = DB::table('user_stats')->select($request->get('leaderboard'))->where('user_id', $request->get('user_id'))->first();

        $response = [
            'user_id'       => (int)$request->get('user_id'),
            'rank'          => $rank,
            'leaderboard'   => $request->get('leaderboard'),
            'stat'          => $stats->{$request->get('leaderboard')},
        ];

        return $this->response->array($response);
    }

    public function leaderboards(Request $request)
    {
        $difference = ((int)$request->get('end') - (int)$request->get('start')) + 1;
        $request->merge(['difference' => $difference]);

        $validator = Validator::make($request->all(), [
            'leaderboard'   => 'required|exists:leaderboards,stat',
            'start'         => 'required|integer|min:0|different:end',
            'end'           => 'required|integer|min:1|different:start|gt_than:start',
            'difference'    => 'required|integer|min:1|max:100'
        ], ['gt_than' => 'End must be greater than start.']);

        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\ResourceException('Invalid request sent.', $validator->errors());
        }

        $board = DB::table('leaderboards')->where('stat', $request->get('leaderboard'))->first();
        $userIds = $this->redis->ZREVRANGE(self::PREFIX . $request->get('leaderboard'), $request->get('start'), $request->get('end'));
      
        if($this->auth->user() && !in_array($this->auth->user()->id, $userIds)) {
            $userIds[] = $this->auth->user()->id;
        }

        $users = DB::table('users')
                ->select(['users.id', 'users.name', 'user_stats.' . $board->stat . ' as stat', 'users.facebook_id'])
                ->join('user_stats', 'user_stats.user_id', '=', 'users.id')
                ->whereIn('users.id', $userIds)
                ->get();

        $desired = array_flip($userIds);
        $sortedUsers = $response = [];

        foreach($users as $key => $user) {
            $user->rank = $this->userRank($board->stat, $user->id);
            $sortedUsers[$desired[$user->id]] = $user;
            if($this->auth->user() && $user->id == $this->auth->user()->id) {
                $response['me'] = $user;
            }
        }

        ksort($sortedUsers);
        $response['users'] = $sortedUsers;
        $response['leaderboard'] = $board;

        return $this->response->array($response);
    }

    private function userRank($board, $userId)
    {
        $score = $this->redis->ZSCORE(self::PREFIX . $board, $userId);
        $member = $this->redis->ZREVRANGEBYSCORE(self::PREFIX . $board, $score, $score, 'LIMIT', 0, 1);
        $rank = $this->redis->ZREVRANK(self::PREFIX . $board, $member[0]) + 1;

        return $rank;
    }
}
