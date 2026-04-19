<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});
Route::view('/info', 'contact')->name('contact');

require __DIR__.'/auth.php';
