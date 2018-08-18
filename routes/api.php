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

Route::group(array('prefix' => 'v1'), function() {
    Route::resource('movies', 'MoviesController');
	Route::group(array('prefix' => 'movies'), function() {
		Route::get('search/{title}', 'MoviesController@search');
		Route::get('search/released/{from}/{to?}', 'MoviesController@searchByReleased');
		Route::get('search/ratings/above/{from}', 'MoviesController@searchMinimumRating');
		Route::get('search/ratings/below/{from}', 'MoviesController@searchMaximumRating');
		Route::get('search/genres/{genres}', 'MoviesController@searchByGenres');
	});
});