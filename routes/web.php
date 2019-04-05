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
Route::group(['namespace' => 'Extranet', 'prefix' => 'extranet'], function () {
    // Admin login routes
    Route::get('/login', ['as' => 'extranet.showLoginForm', 'uses' => 'AuthController@showLoginForm']);
    Route::post('/login', ['as' => 'extranet.login', 'uses' => 'AuthController@login']);

    // All routes inside require admin privileges
    Route::group(['middleware' => ['auth.backend:backend']], function () {
        //
    });
});
