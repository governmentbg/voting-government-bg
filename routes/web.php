<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return response('Welcome to AMS Voting System');
});

//Frontend routes that needs user athorisation
Route::group(['middleware' => ['auth']], function () {
    //
});

// Admin
Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {
    // Admin login routes
    Route::get('/login', ['as' => 'admin.showLoginForm', 'uses' => 'AuthController@showLoginForm']);
    Route::post('/login', ['as' => 'admin.login', 'uses' => 'AuthController@login']);

    // All routes inside require admin privileges
    Route::group(['middleware' => ['auth.backend:backend']], function () {
        Route::get('/votingTours', 'VotingTourController@index');
        Route::get('/votingTours/create','VotingTourController@create');
        Route::get('/votingTours/{id}/edit','VotingTourController@edit');
        Route::post('/votingTours','VotingTourController@store');
        Route::put('/votingTours/{id}','VotingTourController@update');
        
        Route::get('/home', function(){ return 'OK';})->name('admin.home');// TODO for testing only
    });
});
