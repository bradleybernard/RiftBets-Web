<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Log;
use App\Game;

use App\Http\Controllers\Schedule\AnswersController;

class InsertGameQuestionAnswers implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function handle()
    {
        $answersController = new AnswersController();
        $answersController->game = $this->game;
        $answersController->insertAnswers();
    }
}
