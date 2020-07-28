<?php

use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;

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

Route::get('/welcome', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::get('/', 'HomeController@index')->name('home');

Route::middleware('guest')->group(function () {
    Route::view('login', 'auth.login')->name('login');
    // Route::view('register', 'auth.register')->name('register');
});

// Route::view('password/reset', 'auth.passwords.email')->name('password.request');
// Route::get('password/reset/{token}', 'Auth\PasswordResetController')->name('password.reset');

Route::middleware('auth')->group(function () {
    Route::get('/changePassword', 'PasswordController@showChangePasswordForm');
    Route::post('/changePassword', 'PasswordController@changePassword')->name('changePassword');

    // Route::view('email/verify', 'auth.verify')->middleware('throttle:6,1')->name('verification.notice');
    // Route::get('email/verify/{id}/{hash}', 'Auth\EmailVerificationController')->middleware('signed')->name('verification.verify');

    //     Route::view('password/confirm', 'auth.passwords.confirm')->name('password.confirm');

    Route::post('/projectlist', 'ProjectController@list');

    Route::get('/project/select', 'ProjectController@selectList');
    Route::get('/project/{project}/select', 'ProjectController@select');
    Route::resource('/project', 'ProjectController');

    Route::resource('/subject', 'SubjectController');
});

Route::group(['middleware' => ['permission:manage-users']], function () {
    Route::resource('/users', 'UserController', ['except' => ['show']]);
    Route::get('/users/{user}/roles', 'UserController@editroles');
    Route::post('/users/{user}/roles', 'UserController@updateroles');

    Route::resource('/roles', 'RoleController');
    Route::post('/roles/{role}/permissions', 'RoleController@updatepermissions');

    Route::resource('/permissions', 'PermissionController');
});

// Route::group(['middleware' => ['permission:manage-teams,another_study']], function () {
Route::middleware('auth')->group(function () {
    Route::get('/team', 'TeamController@index');
    Route::get('/team/addmember', 'TeamController@addmember');
    Route::post('/team', 'TeamController@storemember');
    Route::get('/team/{user}', 'TeamController@showmember');
    Route::get('/team/{user}/edit', 'TeamController@editmember');
    Route::patch('/team/{user}/update', 'TeamController@updatemember');
    Route::get('/team/{user}/permissions', 'TeamController@editpermissions');
    Route::patch('/team/{user}/permissions', 'TeamController@updatepermissions');
    Route::delete('/team/{user}', 'TeamController@destroymember');
});
