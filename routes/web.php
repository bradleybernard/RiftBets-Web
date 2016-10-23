<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('scrape/leagues', 'Scrape\LeaguesController@scrape');
Route::get('scrape/players', 'Scrape\PlayersController@scrape');
Route::get('scrape/timeline', 'Scrape\TimelineController@scrape');
Route::get('scrape/matchdetails', 'Scrape\MatchDetailsController@scrape');
Route::get('scrape/gamestats', 'Scrape\GameStatsController@scrape');
Route::get('scrape/schedule', 'Scrape\ScheduleController@scrape');

Route::get('poll', 'Schedule\PollingController@poll');
Route::get('answers', 'Schedule\AnswersController@testJob');
Route::get('bets', 'Schedule\GradingController@bets');
Route::get('grade', 'Schedule\GradingController@grade');

Route::get('questions', 'Questions\QuestionsController@insertQuestions');
