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
    protected $description = 'Scrapes all needed sites for testing queries';

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
        $controller = app()->make('App\Http\Controllers\Scrape\LeaguesController');
        $controller->scrape();
        $controller = app()->make('App\Http\Controllers\Scrape\DetailsController');
        $controller->scrape();
        $controller = app()->make('App\Http\Controllers\Scrape\GameStatsController');
        $controller->scrape();
        $controller = app()->make('App\Http\Controllers\Scrape\StatsController');
        $controller->scrape();
        $controller = app()->make('App\Http\Controllers\Scrape\TeamsController');
        $controller->scrape();
        $controller = app()->make('App\Http\Controllers\Scrape\ScheduleController');
        $controller->scrape();
    }
}
