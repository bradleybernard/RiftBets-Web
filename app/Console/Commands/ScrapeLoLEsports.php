<?php

namespace App\Console\Commands;

use Illuminate\Http\Request;
use Illuminate\Console\Command;

class ScrapeLoLEsports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:lolesports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrapes all lolesports data for initial setup.';

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
        
        $this->info('Inserted raw data from Riot Games API');

        app()->make('App\Http\Controllers\Questions\QuestionsController')->insertQuestions();
        $this->info('Inserted questions data');

        app()->make('App\Http\Controllers\Schedule\AnswersController')->testJob();
        $this->info('Inserted answers from questions/gamestats table');

        $this->info('LoLEsports scrape completed successfully!');
    }
}
