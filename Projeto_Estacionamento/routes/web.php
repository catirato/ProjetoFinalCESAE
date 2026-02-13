<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// home page
Route::get('/home', function () {
    return view('homepage');
});

registration route
Route::view('/register', 'login')
   ->middleware('guest')
 ->name('register');

Route::post('/register', Register::class)
    ->middleware('guest');