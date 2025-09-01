<?php

use App\Http\Controllers\Maincontroller;
use Illuminate\Support\Facades\Route;



// Route::get('/show_data', [Maincontroller::class, 'showData']);

Route::get('/', [Maincontroller::class , 'home'])->name('home');
Route::post('prepareGame', [Maincontroller::class , 'prepareGame'])->name('prepareGame');

//in game
Route::get('/game', [Maincontroller::class , 'game'])->name('game');

Route::get('/answer/{answer}', [Maincontroller::class , 'answer'])->name('answer');

Route::get('/next_question', [Maincontroller::class , 'nextQuestion'])->name('next_question');

Route::get('/show_results', [Maincontroller::class , 'showResults'])->name('show_results');



