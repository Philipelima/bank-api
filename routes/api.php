<?php

use App\Http\Controllers\Operation\DepositController;
use App\Http\Controllers\User\AuthController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\RegisterController;


Route::get('/health', fn() => response()->json(['status' => 'ok'], 200));



Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);



Route::prefix('/operation')->middleware(['user.type:common'])->group(function(){

    Route::post('/deposit', [DepositController::class, 'store']);

});