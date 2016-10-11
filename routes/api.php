<?php

use Illuminate\Http\Request;

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
	$api->get('/test', 'App\Http\Controllers\TestController@test');
	$api->get('user', ['middleware' => 'api.auth', function () 
	{
        // This route requires authentication.
        $user = app('Dingo\Api\Auth\Auth')->user();

        return $user;
    }]);

    $api->get('posts', function () 
    {
        // This route does not require authentication.
    });
});