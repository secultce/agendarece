<?php

use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::get('/', fn () => redirect()->route('programmation'));

Route::middleware('auth')->group(function () {
    Route::middleware('role:scheduler,responsible')->group(function () {
        Route::get('espacos-e-categorias', fn () => view('space_category'))->name('space-category');
        Route::get('eixos-e-ocupacoes', fn () => view('axis_occupation'))->name('axis-occupation');
        Route::get('agendas', 'ScheduleController@index')->name('schedule');
        Route::get('datas-comemorativas', 'CustomHolidayController@index')->name('custom-holiday');
    });

    Route::middleware('role:administrator')->group(function () {
        Route::get('equipamento culturales', 'SectorController@index')->name('sector');
    });
    
    Route::middleware('role:responsible')->group(function () {
        Route::get('usuarios', 'UserController@index')->name('user');
        Route::get('logs', 'LogController@index')->name('log');

        route::prefix('configuracoes')->group(function () {
            Route::get('/', 'ConfigurationController@index')->name('configuration');
            Route::post('/', 'ConfigurationController@store')->name('configuration.store');
            Route::put('{configuration}', 'ConfigurationController@update')->name('configuration.update');
        });
    });

    Route::get('programacao', 'ProgrammationController@index')->name('programmation');
    Route::get('programacao/relatorio/{schedule}', 'ProgrammationController@report');

    route::prefix('perfil')->group(function () {
        Route::get('/', 'ProfileController@index')->name('profile');
        Route::put('{user}', 'ProfileController@update')->name('profile.update');
    });
});
