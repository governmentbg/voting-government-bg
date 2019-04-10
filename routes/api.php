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
    Route::post('user/add', 'Api\UserController@add');
    Route::post('user/edit', 'Api\UserController@edit');
    Route::post('user/generateHash', 'Api\UserController@generatePasswordHash');
    Route::post('user/passwordReset', 'Api\UserController@resetPassword');
    Route::post('user/getData', 'Api\UserController@getData');
    Route::post('user/list', 'Api\UserController@list');
    Route::post('user/changePassword', 'Api\UserController@changePassword');

    Route::post('votingTour/add', 'Api\VotingTourController@add');
    Route::post('votingTour/changeStatus', 'Api\VotingTourController@changeStatus');
    Route::post('votingTour/rename', 'Api\VotingTourController@rename');
    Route::post('votingTour/getLatestVotingTour', 'Api\VotingTourController@getLatestVotingTour');
    Route::post('votingTour/list', 'Api\VotingTourController@list');
    Route::post('votingTour/getData', 'Api\VotingTourController@getData');
    Route::post('votingTour/listStatuses', 'Api\VotingTourController@listStatuses');

    Route::post('vote/getLatestVote', 'Api\VoteController@getLatestVote');
    Route::post('vote/isBlockChainValid', 'Api\VoteController@isBlockChainValid');
    Route::post('vote/ranking', 'Api\VoteController@ranking');
    Route::post('vote/vote', 'Api\VoteController@vote');
    Route::post('vote/getVoteStatus', 'Api\VoteController@getVoteStatus');
    Route::post('vote/listVoters', 'Api\VoteController@listVoters');

    Route::post('message/markAsRead', 'Api\MessageController@markAsRead');
    Route::post('message/listByOrg', 'Api\MessageController@listByOrg');
    Route::post('message/listByParentId', 'Api\MessageController@listByParentId');
    Route::post('message/search', 'Api\MessageController@search');
    Route::post('message/listStatuses', 'Api\MessageController@listStatuses');
    Route::post('message/sendMessageToOrg', 'Api\MessageController@sendMessageToOrg');
    Route::post('message/sendMessageFromOrg', 'Api\MessageController@sendMessageFromOrg');

    Route::post('organisation/register', 'Api\OrganisationController@register');
    Route::post('organisation/edit', 'Api\OrganisationController@edit');
    Route::post('organisation/search', 'Api\OrganisationController@search');
    Route::post('organisation/getData', 'Api\OrganisationController@getData');
    Route::post('organisation/getFileList', 'Api\OrganisationController@getFileList');
    Route::post('organisation/listStatuses', 'Api\OrganisationController@listStatuses');
    Route::post('organisation/listCandidateStatuses', 'Api\OrganisationController@listCandidateStatuses');

    Route::post('file/getData', 'Api\FileController@getData');

});

Route::any('{catchall}', 'ApiController@handleMissingRoutes')->where('catchall', '(.*)');
