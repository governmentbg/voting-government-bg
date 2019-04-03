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


Route::middleware(['api'])->group(function () {
    Route::post('user/add', 'Api\UserController@addUser');
    Route::post('user/edit', 'Api\UserController@editUser');
    Route::post('user/generateHash', 'Api\UserController@generatePasswordHash');
    Route::post('user/passwordReset', 'Api\UserController@resetPassword');
    Route::post('user/getData', 'Api\UserController@getData');
    Route::post('user/list', 'Api\UserController@list');
    
    Route::post('votingTours/add', 'Api\VotingTourController@add');
    Route::post('votingTours/changeStatus', 'Api\VotingTourController@changeStatus');
    Route::post('votingTours/rename', 'Api\VotingTourController@rename');
    Route::post('votingTours/getLatestVotingTour', 'Api\VotingTourController@getLatestVotingTour');
    Route::post('votingTours/list', 'Api\VotingTourController@list');
    Route::post('votingTours/getData', 'Api\VotingTourController@getData');
    Route::post('votingTours/listStatuses', 'Api\VotingTourController@listStatuses');

});
