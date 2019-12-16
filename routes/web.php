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

Auth::routes();

Route::match(['get', 'post'], '/SubscriptionService/SendSubscription', 'SubscriptionServiceController@sendSubscription')->name('SendSubscription');
Route::match(['get', 'post'], '/SubscriptionService/test', 'SubscriptionServiceController@test');

Route::get('/','PublicController@index')->name('home');

Route::get('/publicLists/registered', 'PublicController@listRegistered')->name('list.registered');
Route::get('/publicLists/candidates', 'PublicController@listCandidates')->name('list.candidates');
Route::get('/publicLists/voted', 'PublicController@listVoted')->name('list.voted');
Route::get('/publicLists/ranking', 'PublicController@listRanking')->name('list.ranking');
Route::get('/publicLists/registeredAjax', 'PublicController@listRegisteredAjax');
Route::get('/publicLists/candidatesAjax', 'PublicController@listCandidatesAjax');
Route::get('/publicLists/votedAjax', 'PublicController@listVotedAjax');
Route::get('/publicLists/rankingAjax', 'PublicController@listRankingAjax');

Route::group(['middleware' => ['guest', 'guest:backend']], function () {
    Route::get('/register', 'OrganisationController@register')->name('organisation.register');
    Route::post('/predefinedData', 'PredefinedOrganisationController@readData');
    Route::post('/organisations', 'OrganisationController@store')->name('organisation.store');
});

//Frontend routes that need user authorisation
Route::group(['middleware' => ['auth']], function () {
    Route::match(['get', 'post'], '/passwordChange', 'Auth\ResetPasswordController@changePassword')->name('organisation.change_password');

    Route::get('/view', 'OrganisationController@view')->name('organisation.view');
    Route::get('/vote/view', 'VoteController@view')->name('organisation.vote');
    Route::post('/vote/vote', 'VoteController@vote')->name('organisation.vote_action');

    Route::get('/files/download/{id}', 'OrganisationController@downloadFile')->name('fileDowload');

    Route::get('/messages/{id}/list', 'MessagesController@view')->name('organisation.messages');
    Route::get('/messages/new', 'MessagesController@add')->name('organisation.messages.add');
    Route::post('/messages/send/{id?}', 'MessagesController@send')->name('organisation.messages.send');
});

// Admin
Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {
    // Admin login routes
    Route::get('/login', ['as' => 'admin.showLoginForm', 'uses' => 'AuthController@showLoginForm']);
    Route::post('/login', ['as' => 'admin.login', 'uses' => 'AuthController@login']);

    // All routes inside require admin privileges
    Route::group(['middleware' => ['auth.backend:backend']], function () {
        Route::get('/', function () { return view('admin.index'); })->name('admin.index');

        Route::get('/votingTours', 'VotingTourController@index')->name('admin.voting_tour.list');
        Route::get('/votingTours/create','VotingTourController@create')->name('admin.voting_tour.create');
        Route::get('/votingTours/{id}/edit','VotingTourController@edit')->name('admin.voting_tour.edit');
        Route::post('/votingTours','VotingTourController@store')->name('admin.voting_tour.store');
        Route::put('/votingTours/{id}','VotingTourController@update')->name('admin.voting_tour.update');
        Route::get('/votingTours/{id}/ranking','VotingTourController@ranking')->name('admin.ranking');

        Route::get('/settings', 'OrganisationController@settings')->name('admin.settings');

        Route::get('/messages', 'MessagesController@list')->name('admin.messages.list');
        Route::post('/messages/send/{id?}', 'MessagesController@send')->name('admin.messages.send');
        Route::get('/messages/{id}/{orgId?}', 'MessagesController@view')->name('admin.messages');

        Route::match(['get', 'post'], '/passwordChange', 'AuthController@changePassword')->name('admin.change_password');

        Route::get('/logout', 'AuthController@logout')->name('admin.logout');
        Route::get('/organisations', 'OrganisationController@list')->name('admin.org_list');
        Route::post('/organisations/update/{id}', 'OrganisationController@update')->name('admin.org_update');
        Route::get('/organisations/edit/{id}', 'OrganisationController@edit')->name('admin.org_edit');
        Route::get('/organisations/files/download/{id}', 'OrganisationController@downloadFile')->name('admin.fileDowload');
        Route::get('/organisations/{id}/messages/new', 'MessagesController@add')->name('admin.messages.add');
        Route::get('/votingTours/{id}/rankingAdminAjax', 'VotingTourController@listAdminRankingAjax');
        Route::get('/actionsHistory', 'ActionsHistoryController@list')->name('admin.actions_history');

        // SYSTEM user routes
        Route::group(['middleware' => 'auth.system:backend'], function () {
            Route::get('/committees', 'CommitteeController@list')->name('admin.committee.list');
            Route::get('/committee/add', 'CommitteeController@create')->name('admin.committee.add');
            Route::get('/committee/edit/{id}', 'CommitteeController@edit')->name('admin.committee.edit');
            Route::post('/committee', 'CommitteeController@store')->name('admin.committee.store');
            Route::put('/committee/{id}', 'CommitteeController@update')->name('admin.committee.update');
        });
    });
});
