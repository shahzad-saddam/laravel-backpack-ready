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

// forgot and reset routes
Route::post('forgot', 'Auth\ForgotPasswordController@getResetToken');
Route::post('reset', 'Auth\ResetPasswordController@reset');
Route::post('login', 'Api\AuthController@login');
Route::post('register', 'Api\AuthController@register');

Route::group(['as' => 'api.', 'namespace' => 'Api', 'middleware' => 'auth:api'], function () {

    Route::get('users/me', 'UserController@show')->name('user.show');
    Route::post('users/me', 'UserController@update')->name('user.update');
});

