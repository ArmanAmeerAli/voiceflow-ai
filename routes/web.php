<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiRequired;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/voiceflow', function () {
    return "You may Pass!";
})->middleware(ApiRequired::class);