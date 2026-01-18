<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RegisterController;
use App\Http\Middleware\CheckUserMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(CheckUserMiddleware::class)->get('/', [HomeController::class, 'index'])->name('home');

Route::name('register.')->prefix('register')->group(function () {
    Route::get('/', [RegisterController::class, 'get'])->name('get');
    Route::post('/', [RegisterController::class, 'post'])->name('post');
});

Route::name('login.')->prefix('login')->group(function () {
    Route::get('/', [LoginController::class, 'get'])->name('get');
    Route::post('/', [LoginController::class, 'post'])->name('post');
});

Route::post('logout', [LogoutController::class, 'post'])->name('logout');
