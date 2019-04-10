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
    return view('home.index');
});

Route::get('/register', function () {
    return view('organisation.register');
});

Route::get('/view', function () {
    return view('organisation.view');
});

Route::get('/list', function () {
    return view('tours.list');
});

Route::get('/edit', function () {
    return view('tours.edit');
});

Route::get('/request', function () {
    return view('organisation.request');
});

Route::get('admin/organisations', function () {
    return view('admin.orglist');
});

Route::get('/admin/committeeAdd', function () {
    return view('admin.committeeAdd');
});

Route::get('/admin/committeeEdit', function () {
    return view('admin.committeeEdit');
});

Route::get('/admin/committeeList', function () {
    return view('admin.committeeList');
});

Route::get('/admin/passwordChange', function () {
    return view('admin.passwordChange');
});

Route::get('/admin/tour/add', function () {
    return view('tours.add');
});

Route::get('/organisation/settings', function () {
    return view('organisation.settings');
});

Route::get('/organisation/vote', function () {
    return view('organisation.vote');
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
        //
    });
});

