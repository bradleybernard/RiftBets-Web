<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use App\Game;
use App\Jobs\InsertGameQuestionAnswers;

use DB;
use Log;

class AnswersController extends Controller
{
    protected $questions;

    public      $game;
    protected   $gameStats;
    protected   $gameTeamStats;
    protected   $gamePlayerStats;

    public function testJob()
    {
        dispatch(new InsertGameQuestionAnswers(Game::where('game_id', '1001890201')->first()));
    }

    public function insertAnswers()
    {
        $answers = [];

        $this->questions          = DB::table('questions')->select(['id', 'slug'])->get();
        $this->gameStats          = DB::table('game_stats')->where('game_id', $this->game->game_id)->first();
        $this->gameTeamStats      = DB::table('game_team_stats')->where('game_id', $this->game->game_id)->get();
        $this->gamePlayerStats    = DB::table('game_player_stats')->where('game_id', $this->game->game_id)->get();

        foreach($this->questions as $question) {
            $answers[] = [
                'question_id'   => $question->id,
                'game_id'       => $this->game->game_id,
                'answer'        => $this->{camel_case($question->slug)}(),
            ];
        }

        DB::table('question_answers')->insert($answers);
    }

    private function playerTopTeamOneChampion()
    {
        return $this->gamePlayerStats->where('participant_id', 1)->pluck('champion_id')->first();
    }

    private function teamWin()
    {
        return $this->gameTeamStats->where('win', true)->pluck('team_id')->first();
    }

    private function gameDuration()
    {
        return $this->gameStats->game_duration;
    }

    private function playerMidTeamOneKills()
    {
        return $this->gamePlayerStats->where('participant_id', 3)->pluck('kills')->first();
    }
}
