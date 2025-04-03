<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\RegisterController;


Route::get('/health', fn() => response()->json(['status' => 'ok'], 200));



Route::post('/register', [RegisterController::class, 'store']);
