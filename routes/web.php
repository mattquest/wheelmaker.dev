<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::post('/contact', [ContactController::class, 'post'])->middleware('throttle:5,1');
Route::post('/download-resume', [DownloadController::class, 'resume'])->middleware('throttle:10,1');
