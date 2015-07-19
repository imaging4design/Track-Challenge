<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
//Route::get('/', array('as' => 'profile', 'uses' => 'StatsController@index'));

// HOME ROUTE
Route::get('/', function() {
	return View::make('index');
});


// AUTHENTICATION & PASSWORD RESET ROUTES
Route::post('auth/login', array('before' => 'csrf_json', 'uses' => 'AuthController@login'));
Route::post('auth/getGravatar', 'AuthController@getGravatar');
Route::post('auth/register', array('before' => 'csrf_json', 'uses' => 'AuthController@register'));
Route::get('auth/logout', 'AuthController@logout');
Route::post('auth/resetPassword', array('before' => 'csrf_json', 'uses' => 'AuthController@resetPassword'));
Route::get('auth/recover/{code}', 'AuthController@getRecover');
Route::post('auth/setPassword', array('before' => 'csrf', 'uses' => 'AuthController@setPassword'));



// ATHLETES ROUTE
Route::group(array('prefix' => 'athletes'), function() {
	Route::get('selections/{id}/{gender}', 'AthletesController@show');
	Route::get('selections/{id}/{gender}/{user}', 'AthletesController@completed');
	Route::resource('selections', 'AthletesController');
});



// SELECTIONS ROUTE
//Route::group(array('before' => 'auth', 'prefix' => 'selections'), function() {
Route::group(array('prefix' => 'selections'), function() {
	Route::resource('selections', 'SelectionsController');
	Route::get('selections/{user_id}/{event_id}/{gender}', 'SelectionsController@find_user_picks');
});

Route::get('selections/{id}/{gender}', 'SelectionsController@find_all_user_picks');


// EMAIL FRIENDS ROUTE
Route::get('email/{id}/{email}', 'SelectionsController@email_picks');


// STATS (PICKS) ROUTE
Route::get('stats/{event_id}/{gender}', 'StatsController@show');



// 404 ERROR ROUTE
App::missing(function() {
	return Response::view('errors.error404', array(), 404);
});