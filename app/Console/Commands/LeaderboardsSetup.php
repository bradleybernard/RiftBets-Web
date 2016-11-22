<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LeaderboardsSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert all leaderboard redis keys into our relational database.';

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
        $controller = new \App\Http\Controllers\Leaderboards\LeaderboardsController;
        $controller->setupTable();

        $this->info("Inserted Redis keys into the database!");
    }
}
