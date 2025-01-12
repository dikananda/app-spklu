<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('map');
});

Route::get('/admin', function () {
    return view('admin');
})->middleware(['auth', 'verified'])->name('admin');

require __DIR__.'/auth.php';
