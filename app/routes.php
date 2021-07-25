<?php

use App\Controllers\AuthController;
use App\Controllers\BaseController;
use App\Controllers\HomeController;
use SquareMvc\Foundation\Router\Route;

return [
    'index' => Route::get('/', [BaseController::class, 'index']),

    // Authentication
    'register.form' => Route::get('/inscription', [AuthController::class, 'registerForm']),
    'register.request' => Route::post('/inscription', [AuthController::class, 'register']),
    'login.form' => Route::get('/connexion', [AuthController::class, 'loginForm']),
    'login.request' => Route::post('/connexion', [AuthController::class, 'login']),

    // User
    'home' => Route::get('/compte', [HomeController::class, 'index']),
];
