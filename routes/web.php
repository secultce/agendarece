<?php

use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::get('/', fn () => redirect()->route('programmation'));

Route::middleware('auth')->group(function () {
    Route::middleware('role:scheduler')->group(function () {
        Route::get('espacos-e-categorias', fn () => view('space_category'))->name('space-category');
    });

    Route::middleware('role:administrator')->group(function () {
        Route::get('usuarios', 'UserController@index')->name('user');
    });

    Route::get('programacao', 'ProgrammationController@index')->name('programmation');
    Route::get('perfil', 'ProfileController@index')->name('profile');
});
