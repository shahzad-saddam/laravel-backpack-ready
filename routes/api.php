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

Route::prefix('auth')
    ->as('auth.')
    ->group(function () {
        Route::post('login', 'AuthController@login')->name('login');
        Route::post('logout', 'AuthController@logout')->name('logout');
        Route::post('refresh', 'AuthController@refresh')->name('refresh');
        Route::post('register', 'AuthController@register')->name('register');
        Route::post('forgot', 'AuthController@forgot')->name('forgot');
        Route::post('forgot/reset', 'AuthController@reset')->name('forgot.reset');
        Route::post('validate', 'AuthController@validateEmail')->name('validate');
        Route::post('validate/resend', 'AuthController@validateResend')->name('validate.resend');
        Route::post('social/{provider}', 'AuthController@social')->name('social');
    });

Route::resource('country', 'CountryController', ['only' => ['index', 'show']]);
