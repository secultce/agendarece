<?php

use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::get('/', fn () => redirect()->route('programmation'));

Route::middleware('auth')->group(function () {
    Route::middleware('role:scheduler')->group(function () {
        Route::get('espacos-e-categorias', fn () => view('space_category'))->name('space-category');
        Route::get('Agendas', 'ScheduleController@index')->name('schedule');
    });

    Route::middleware('role:administrator')->group(function () {
        route::prefix('configuracoes')->group(function () {
            Route::get('/', 'ConfigurationController@index')->name('configuration');
            Route::post('/', 'ConfigurationController@store')->name('configuration.store');
            Route::put('{configuration}', 'ConfigurationController@update')->name('configuration.update');
        });

        Route::get('logs', 'LogController@index')->name('log');
        Route::get('usuarios', 'UserController@index')->name('user');
    });

    Route::get('programacao', 'ProgrammationController@index')->name('programmation');

    route::prefix('perfil')->group(function () {
        Route::get('/', 'ProfileController@index')->name('profile');
        Route::put('{user}', 'ProfileController@update')->name('profile.update');
    });
});
