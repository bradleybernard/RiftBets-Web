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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('scrape/leagues', 'Scrape\LeaguesController@scrape');
Route::get('scrape/teams', 'Scrape\TeamsController@scrape');
Route::get('scrape/stats', 'Scrape\StatsController@scrape');
Route::get('scrape/details', 'Scrape\DetailsController@scrape');