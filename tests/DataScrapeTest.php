<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DataScrapeTest extends TestCase
{
    // use DatabaseMigrations;

    protected static $dbSeeded = false;

    // Setup database seed by scraping
    protected static function initDB()
    {
        $refresh = Artisan::call('migrate:refresh');
        $scrapeDDragon = Artisan::call('scrape:ddragon');
        $scrapeLolesports = Artisan::call('scrape:lolesports');
        $leaderboards = Artisan::call('leaderboards:setup');
    }

    // Calls method once when test class created
    public function setUp()
    {
        parent::setUp();

        if (!static::$dbSeeded) {
            static::initDB();
            static::$dbSeeded = true;
        }
    }

    // Go thru all tables and check each has at least one row
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

    // Select random datetime and chop off time part and hit the API and see if it appears
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

    // Select random match and query the details of it and check it works
    public function test_match_details_outputs_correctly()
    {
        $match = DB::table('matches')->select('api_id_long')->orderBy(DB::raw('RAND()'))->first();

        $this->get('/api/match?match_id=' . $match->api_id_long)
             ->seeJsonStructure([
                'api_id_long', 
                'name', 
                'state',
            ]);
    }

    // Create a random user and see if it exists in the leaderboards board
    public function test_leaderboards_output()
    {
        $this->createUser();
        
        $this->get('api/leaderboards?leaderboard=weekly_wins&start=0&end=99')
             ->seeJsonStructure([
                'users',
                'leaderboard' => [
                    'stat', 
                    'timeframe', 
                    'title', 
                    'prize'
                ]
            ]);
    }

    // Make sure user has a rank in a random leaderboard
    public function test_leaderboards_rank_output()
    {
        $this->get('api/leaderboards/rank?leaderboard=weekly_wins&user_id=1')
             ->seeJsonStructure([
                "user_id",
                "rank",
                "leaderboard",
                "stat",
            ]);
    }

    // Make sure this new user has a profile
    public function test_profile_output()
    {
        $this->get('api/profile?user_id=1')
             ->seeJsonStructure([
                "user_info",
                "user_stats",
                "leaderboard_stats",
                "games",
            ]);
    }

    // Helper function to create a new user
    private function createUser() 
    {
        $accessToken = 'EAAKzu2L3NZCIBANqtTkEcqUqsOS0HaQAOOiTJaPSU2MlAV2ZBDSvSZCMpy6qAlUXTDKQK3UxKrFm5tZAmHofK2krZArEZBluCFo3lkZCbH437pi4DZBFFUmcHgZBQSmPPMfXZAkrgmPFOhjxZAYS7ro9ggPFIQKZA8PwoUZCwjI0jP3u9UwZDZD';

        $this->json('POST', '/api/auth/facebook', ['facebook_access_token' => $accessToken])
             ->seeJsonStructure([
                'token',
                'user' => [
                    'id', 
                    'facebook_id',
                    'name', 
                    'email', 
                    'credits',
                    'device_token',
                    'created_at',
                    'updated_at',
                ]
        ]);
    }
}
