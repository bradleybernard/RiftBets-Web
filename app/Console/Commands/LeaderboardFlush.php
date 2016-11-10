<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Redis;
use DB;

class LeaderboardFlush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:reload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reload (delete and upload) Redis database with all user stats.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = 1;
        $deleteStr = '';

        $redis = Redis::connection();
        $leaderboards = DB::table('leaderboards')->get();

        foreach($leaderboards as $leaderboard)
        {
            $deleteStr .= $leaderboard->redis_key . " ";
        }

        $redis->DEL(substr($deleteStr, 0, -1));

        DB::table('user_stats')->chunk(750, function($stats) use ($redis, &$count, $leaderboards)
        {
            $redis->pipeline(function ($pipe) use ($stats, $leaderboards)
            {
                foreach($stats as $stat)
                {
                    foreach($leaderboards as $leaderboard)
                    {
                        $pipe->ZADD($leaderboard->redis_key, $stat->{$leaderboard->stat}, $stat->user_id);
                    }
                }

            });
            
            echo "Done with " . ($count++ * 750) . " rows.\n";

        });
    }
}
