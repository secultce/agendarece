<?php

use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::get('/', fn () => redirect()->route('home'));
Route::get('home', 'HomeController@index')->name('home');
