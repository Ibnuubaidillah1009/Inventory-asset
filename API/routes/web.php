<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', fn () => response()->json([
    'message' => 'Unauthenticated. Silakan login kembali.',
], 401))->name('login');
