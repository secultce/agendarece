<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::middleware('role:scheduler')->group(function () {
        Route::prefix('space')->group(function () {
            Route::get('/', 'Api\\SpaceController@list');
            Route::post('/', 'Api\\SpaceController@store');
            Route::put('{space}', 'Api\\SpaceController@update');
            Route::put('{space}/toggle-activation', 'Api\\SpaceController@toggleActivation');
            Route::delete('{space}', 'Api\\SpaceController@destroy');
        });

        Route::prefix('category')->group(function () {

        });
    });

    Route::middleware('role:administrator')->group(function () {
        Route::get('role', 'Api\\RoleController@list');

        Route::prefix('user')->group(function () {
            Route::get('/', 'Api\\UserController@list');
            Route::post('/', 'Api\\UserController@store');
            Route::put('{user}', 'Api\\UserController@update');
            Route::put('{user}/toggle-activation', 'Api\\UserController@toggleActivation');
            Route::delete('{user}', 'Api\\UserController@destroy');
        });
    });
});
