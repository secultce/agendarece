<?php

use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::get('/', fn () => redirect()->route('programmation'));

Route::middleware('auth')->group(function () {
    Route::middleware('role:scheduler')->group(function () {
        Route::get('espacos', 'SpaceController@index')->name('space');
        Route::get('categorias', 'CategoryController@index')->name('category');
    });

    Route::middleware('role:administrator')->group(function () {
        Route::get('usuarios', 'UserController@index')->name('user');
    });

    Route::get('programacao', 'ProgrammationController@index')->name('programmation');
    Route::get('configuracoes', 'ConfigurationController@index')->name('configuration');
    Route::get('perfil', 'ProfileController@index')->name('profile');
});
