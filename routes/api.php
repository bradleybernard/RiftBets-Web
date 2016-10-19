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
    $api->post('auth/facebook', 'App\Http\Controllers\FacebookController@facebook');

    $api->get('query', 'App\Http\Controllers\TestController@query');
    $api->get('schedule', 'App\Http\Controllers\Queries\ScheduleController@query');

    // $api->get('test', 'App\Http\Controllers\Test\TestController@test');
    // $api->get('token', 'App\Http\Controllers\Test\TestController@generate');
});
