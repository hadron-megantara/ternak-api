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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(array('prefix' => 'v1'), function(){
    Route::post('/login', 'Auth\LoginController@login');
    Route::post('/register', 'Auth\RegisterController@register');

    Route::group(array('prefix' => 'master'), function(){
        Route::get('/province', 'MasterController@getProvince');
        Route::get('/city', 'MasterController@getCity');
        Route::get('/district', 'MasterController@getDistrict');
        Route::get('/village', 'MasterController@getVillage');
    });

    Route::middleware(['auth:api'])->group(function () {
        Route::group(array('prefix' => 'account'), function(){
            Route::put('/profile', 'Account\ProfileController@updateProfile');
        });
    });
});
