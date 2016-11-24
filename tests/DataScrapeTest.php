<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DataScrapeTest extends TestCase
{
    use DatabaseMigrations;

    protected static $dbSeeded = false;

    protected static function initDB()
    {
        $refresh = Artisan::call('migrate:refresh');
        $scrapeDDragon = Artisan::call('scrape:ddragon');
        $scrapeLolesports = Artisan::call('scrape:lolesports');
    }

    public function setUp()
    {
        parent::setUp();

        if (!static::$dbSeeded) {
            static::initDB();
            static::$dbSeeded = true;
        }
    }

    public function test_all_tables_have_data()
    {
        $tables = ['tournaments', 'teams', 'players', 'team_players', 'schedule', 'rosters','matches', 'leagues', 'games', 
                    'game_videos', 'game_team_stats', 'game_stats', 'game_player_stats', 'game_mappings', 'game_frame_player_stats',
                    'game_events', 'game_event_details', 'ddragon_summoners', 'ddragon_profile_icons', 'ddragon_items', 'ddragon_champions',
                    'breakpoints', 'breakpoint_resources', 'brackets', 'bracket_resources', 'bracket_records'];

        foreach($tables as $table) {
            $this->assertTrue(DB::table($table)->count() > 0);
        }
    }

     public function test_match_schedule_outputs_matches_grouped_by_date_test()
    {
        $row = DB::table('schedule')->where('api_tournament_id', '3c5fa267-237e-4b16-8e86-20378a47bf1c')->first();

        $firstDate = substr($row->scheduled_time, 0, 10); //2016-09-30 02:30:00 --> '2016-09-30'

        $this->get('/api/schedule')
             ->seeJsonStructure([
                $firstDate => [
                    '*' => [
                        'name', 
                        'state', 
                        'api_id_long',
                        'scheduled_time',
                    ]
                 ]
            ]);
    }
}
