<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MatchScheduleOutputsMatchesGroupedByDateTest extends TestCase
{
    public function test_match_schedule_outputs_matches_grouped_by_date_test()
    {
        $firstDate = '2016-09-29';

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
