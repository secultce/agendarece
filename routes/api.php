<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('space', 'Api\\SpaceController@list');
    Route::get('category', 'Api\\CategoryController@list');
    Route::get('programmation', 'Api\\ProgrammationController@list');
    
    Route::middleware('role:scheduler')->group(function () {
        Route::get('user/{role?}', 'Api\\UserController@list');

        Route::prefix('programmation')->group(function () {
            Route::post('/', 'Api\\ProgrammationController@store');
            Route::put('{programmation}', 'Api\\ProgrammationController@update');
            Route::put('{programmation}/date', 'Api\\ProgrammationController@updateDates');
            Route::delete('{programmation}', 'Api\\ProgrammationController@destroy');
            
            Route::prefix('{programmation}/link')->group(function () {
                Route::get('/', 'Api\\ProgrammationLinkController@list');
                Route::post('/', 'Api\\ProgrammationLinkController@store');
                Route::put('{link}', 'Api\\ProgrammationLinkController@update');
                Route::delete('{link}', 'Api\\ProgrammationLinkController@destroy');
            });

            Route::prefix('{programmation}/note')->group(function () {
                Route::get('/', 'Api\\ProgrammationNoteController@list');
                Route::post('/', 'Api\\ProgrammationNoteController@store');
                Route::put('{note}', 'Api\\ProgrammationNoteController@update');
                Route::delete('{note}', 'Api\\ProgrammationNoteController@destroy');
            });

            Route::prefix('{programmation}/comment')->group(function () {
                Route::get('/', 'Api\\ProgrammationCommentController@list');
                Route::post('/', 'Api\\ProgrammationCommentController@store');
                Route::put('{comment}', 'Api\\ProgrammationCommentController@update');
                Route::delete('{comment}', 'Api\\ProgrammationCommentController@destroy');
            });
        });

        Route::prefix('space')->group(function () {
            Route::post('/', 'Api\\SpaceController@store');
            Route::put('{space}', 'Api\\SpaceController@update');
            Route::put('{space}/toggle-activation', 'Api\\SpaceController@toggleActivation');
            Route::delete('{space}', 'Api\\SpaceController@destroy');
        });

        Route::prefix('category')->group(function () {
            Route::post('/', 'Api\\CategoryController@store');
            Route::put('{category}', 'Api\\CategoryController@update');
            Route::delete('{category}', 'Api\\CategoryController@destroy');
        });
    });

    Route::middleware('role:administrator')->group(function () {
        Route::get('role', 'Api\\RoleController@list');

        Route::prefix('user')->group(function () {
            Route::post('/', 'Api\\UserController@store');
            Route::put('{user}', 'Api\\UserController@update');
            Route::put('{user}/toggle-activation', 'Api\\UserController@toggleActivation');
            Route::delete('{user}', 'Api\\UserController@destroy');
        });
    });
});
