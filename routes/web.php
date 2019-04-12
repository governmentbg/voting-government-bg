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

Route::get('/', function () {
    return view('home.index');
})->name('home');

//================START test routes

Route::get('/admin/committeeAdd', function () {
    return view('admin.committeeAdd');
});

Route::get('/admin/committeeEdit', function () {
    return view('admin.committeeEdit');
});

Route::get('/admin/committeeList', function () {
    return view('admin.committeeList');
});

Route::get('/admin/tour/add', function () {
    return view('tours.add');
});

Route::get('/organisation/vote', function () {
    return view('organisation.vote');
});
//================END test routes

Route::get('/register','OrganisationController@register')->name('organisation.register');
Route::get('createcaptcha', 'CaptchaController@create');
Route::get('refreshcaptcha', 'CaptchaController@refreshCaptcha');
Route::post('/organisations','OrganisationController@store')->name('organisation.store');

//Frontend routes that needs user athorisation
Route::group(['middleware' => ['auth']], function () {
    Route::get('/settings', function () {
        return view('organisation.settings');
    })->name('organisation.settings');

    Route::get('/passwordChange', function () {
            return view('auth.password_change');
        })->name('organisation.change_password');

    Route::post('/passwordChange', 'Auth\ResetPasswordController@changePassword')->name('organisation.change_password');

    Route::get('/view', 'OrganisationController@view')->name('organisation.view');

    Route::get('/messages/{id}', function () { return view('organisation.request'); })->name('organisation.messages');
    Route::get('/files/download/{id}', 'OrganisationController@downloadFile')->name('fileDowload');
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

        Route::get('/settings', function () {
            return view('organisation.settings');
        })->name('admin.settings');

        Route::get('/passwordChange', function () {
            return view('auth.password_change');
        })->name('admin.change_password');
        Route::post('/passwordChange', 'AuthController@changePassword');

        Route::get('/logout', 'AuthController@logout')->name('admin.logout');
        Route::get('/organisations', 'OrganisationController@list')->name('admin.org_list');
        Route::post('/organisations/update/{id}', 'OrganisationController@update')->name('admin.org_edit');
        Route::get('/organisations/edit/{id}', 'OrganisationController@edit')->name('admin.org_edit');
        Route::get('/organisations/files/download/{id}', 'OrganisationController@downloadFile')->name('admin.fileDowload');

        Route::get('/home', function(){ return 'OK';})->name('admin.home');// TODO for testing only
    });
});

