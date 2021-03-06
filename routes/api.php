<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) 
{
    $api->post('auth/facebook', 'App\Http\Controllers\Facebook\FacebookController@facebook');

    $api->get('schedule', 'App\Http\Controllers\Queries\ScheduleController@query');
    $api->get('match', 'App\Http\Controllers\Queries\MatchDetailsController@query');

    #$api->get('match/bettable', 'App\Http\Controllers\Queries\MatchDetailsController@bettable');

    $api->get('leaderboards', 'App\Http\Controllers\Leaderboards\LeaderboardsController@leaderboards');
    $api->get('leaderboards/rank', 'App\Http\Controllers\Leaderboards\LeaderboardsController@rank');
    $api->get('leaderboards/around', 'App\Http\Controllers\Leaderboards\LeaderboardsController@around');
    $api->get('profile', 'App\Http\Controllers\Queries\UserProfileController@query');
    
    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->post('bets/create', 'App\Http\Controllers\Bets\BetsController@bet');
        $api->post('user/bets', 'App\Http\Controllers\Queries\UserBetsController@query');
        $api->post('cards/create', 'App\Http\Controllers\Queries\CardController@generate');
    });
});
