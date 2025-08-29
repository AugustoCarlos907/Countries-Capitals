<?php

use App\Http\Controllers\Maincontroller;
use Illuminate\Support\Facades\Route;



// Route::get('/show_data', [Maincontroller::class, 'showData']);

Route::view('/', 'home');