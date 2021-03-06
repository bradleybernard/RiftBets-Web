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
Route::get('token', function() {
    return JWTAuth::fromUser(App\User::find(1));
});

Route::get('push', 'Push\PushNotificationController@push');

Route::get('setup', 'Leaderboards\LeaderboardsController@setup');
Route::get('populate', 'Leaderboards\LeaderboardsController@populate');
Route::get('leaderboard', 'Leaderboards\LeaderboardsController@leaderboard');

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

Route::get('testpush', 'Push\PushNotificationController@test');

Route::get('questions', 'Questions\QuestionsController@insertQuestions');

Route::get('profile', 'Queries\UserProfileController@query');