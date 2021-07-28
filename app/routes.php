<?php

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\PostController;
use SquareMvc\Foundation\Router\Route;

return [
    'index' => Route::get('/', [PostController::class, 'index']),

    // Authentication
    'register.form' => Route::get('/inscription', [AuthController::class, 'registerForm']),
    'register.request' => Route::post('/inscription', [AuthController::class, 'register']),
    'login.form' => Route::get('/connexion', [AuthController::class, 'loginForm']),
    'login.request' => Route::post('/connexion', [AuthController::class, 'login']),
    'logout' => Route::post('/deconnexion', [AuthController::class, 'logout']),

    // User
    'home' => Route::get('/compte', [HomeController::class, 'index']),
    'home.updateName' => Route::patch('/compte', [HomeController::class, 'updateName']),
    'home.updateEmail' => Route::patch('/compte/email', [HomeController::class, 'updateEmail']),
    'home.updatePassword' => Route::patch('/compte/password', [HomeController::class, 'updatePassword']),

    // Post
    'posts.create' => Route::get('/posts/creer', [PostController::class, 'create']),
    'posts.store' => Route::post('/posts/creer', [PostController::class, 'store']),
    'posts.edit' => Route::get('/posts/{slug}/modifier', [PostController::class, 'edit']),
    'posts.update' => Route::patch('/posts/{slug}/modifier', [PostController::class, 'update']),
    'posts.show' => Route::get('/posts/{slug}', [PostController::class, 'show']),
    'posts.comment' => Route::post('/posts/{slug}', [PostController::class, 'comment']),
    'posts.delete' => Route::delete('/posts/{slug}', [PostController::class, 'delete']),
];
