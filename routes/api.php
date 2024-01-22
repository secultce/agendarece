<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('space/{sector?}', 'Api\\SpaceController@list');
    Route::get('category/{sector?}', 'Api\\CategoryController@list');
    Route::get('schedule/{sector?}', 'Api\\ScheduleController@list');
    Route::get('custom-holiday/{sector?}', 'Api\\CustomHolidayController@list');
    Route::get('occupation/{sector?}', 'Api\\OccupationController@list');
    Route::get('axis/{sector?}', 'Api\\AxisController@list');
    Route::get('programmation', 'Api\\ProgrammationController@list');
    Route::get('sector', 'Api\\SectorController@list');
    Route::put('user/toggle-dark-mode', 'Api\\UserController@toggleDarkMode');
    
    Route::middleware('role:scheduler,responsible')->group(function () {
        Route::get('user/{role?}', 'Api\\UserController@list');
        Route::post('solicitation', 'Api\\SolicitationController@store');

        Route::prefix('schedule')->group(function () {
            Route::post('/', 'Api\\ScheduleController@store');
            Route::put('{schedule}', 'Api\\ScheduleController@update');
            Route::put('{schedule}/toggle-status', 'Api\\ScheduleController@toggleStatus');
            Route::delete('{schedule}', 'Api\\ScheduleController@destroy');
        });

        Route::prefix('custom-holiday')->group(function () {
            Route::post('/', 'Api\\CustomHolidayController@store');
            Route::put('{customHoliday}', 'Api\\CustomHolidayController@update');
            Route::delete('{customHoliday}', 'Api\\CustomHolidayController@destroy');
        });

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

        Route::prefix('occupation')->group(function () {
            Route::post('/', 'Api\\OccupationController@store');
            Route::put('{occupation}', 'Api\\OccupationController@update');
            Route::delete('{occupation}', 'Api\\OccupationController@destroy');
        });

        Route::prefix('axis')->group(function () {
            Route::post('/', 'Api\\AxisController@store');
            Route::put('{axis}', 'Api\\AxisController@update');
            Route::delete('{axis}', 'Api\\AxisController@destroy');
        });
    });

    Route::middleware('role:administrator')->group(function () {
        Route::prefix('sector')->group(function () {
            Route::post('/', 'Api\\SectorController@store');
            Route::put('{sector}', 'Api\\SectorController@update');
            Route::put('{sector}/toggle-activation', 'Api\\SectorController@toggleActivation');
            Route::delete('{sector}', 'Api\\SectorController@destroy');
        });
    });

    Route::middleware('role:responsible')->group(function () {
        Route::get('role', 'Api\\RoleController@list');
        Route::get('log', 'Api\\LogController@list');
        
        Route::prefix('user')->group(function () {
            Route::post('/', 'Api\\UserController@store');
            Route::put('{user}', 'Api\\UserController@update');
            Route::put('{user}/toggle-activation', 'Api\\UserController@toggleActivation');
            Route::delete('{user}', 'Api\\UserController@destroy');
        });
    });
});
