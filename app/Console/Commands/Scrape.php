<?php

namespace App\Console\Commands;

use Illuminate\Http\Request;
use Illuminate\Console\Command;

class Scrape extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrapes all riot/match history data for initial setup.';

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
        $controllers = ['Leagues', 'MatchDetails', 'GameStats', 'Timeline', 'Players', 'Schedule'];

        foreach($controllers as $controller) {
            app()->make('App\Http\Controllers\Scrape\\' . $controller . 'Controller')->scrape();
        }
    }
}
