<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});
//Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
//Route::post('/reset-password/', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');


Route::get('get/user', [AuthController::class, 'loggedInUser'])->middleware('auth:sanctum');


