<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use DB;
use Redis;

class UpdateLeaderboards implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $redis = Redis::connection();
        $count = 0;

        DB::table('user_stats')->where('redis_update', 1)->chunk(750, function ($stats) use ($redis, &$count)
        {
            $redis->pipeline(function ($pipe) use ($stats)
            {
                foreach($stats as $stat)
                {
                    $pipe->ZADD('lb_weekly_wins', $stat->weekly_wins, $stat->user_id);
                    $pipe->ZADD('lb_monthly_wins', $stat->monthly_wins, $stat->user_id);
                    $pipe->ZADD('lb_alltime_wins', $stat->alltime_wins, $stat->user_id);

                    $pipe->ZADD('lb_weekly_streak', $stat->weekly_streak, $stat->user_id);
                    $pipe->ZADD('lb_monthly_streak', $stat->monthly_streak, $stat->user_id);
                    $pipe->ZADD('lb_alltime_streak', $stat->alltime_streak, $stat->user_id);
                }

            });
            
            DB::table('user_stats')->where('redis_update', 1)->skip((750 * $count++))->take(750)->update(['redis_update' => 0]);

        });
    }
}
