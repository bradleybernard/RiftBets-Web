<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrapeDDragon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:ddragon {api_version?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape DDragon static data: champs, icons, summoners, items from Riot Games API.';

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
        $ddragon = new \App\Http\Controllers\Scrape\DDragonController();
        $ddragon->scrape($this->argument('api_version'));

        $this->info('DDragon scrape completed successfully!');
    }
}
