<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(TokenController::class)
->prefix('token')
->group(function () {
    Route::post('/fetch',  'fetch');
    Route::post('/revoke', 'revoke')->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')
->group(function () {

    Route::controller(UserController::class)
    ->prefix('users')
    ->name('users.')
    ->group(function () {
        Route::get('/',                  'index')->name('index');
        Route::get('/{id}',              'show')->name('show');
        Route::patch('/{user}/follow',   'follow')->name('follow');
        Route::patch('/{user}/unfollow', 'unfollow')->name('unfollow');
    });
    
    Route::controller(PostController::class)
    ->prefix('posts')
    ->name('posts.')
    ->group(function () {
        Route::get('/',                'index')->name('index');
        Route::put('/{post}',          'update')->name('update')->can('update', 'post');
        Route::delete('/{post}',       'destroy')->name('destroy')->can('destroy', 'post');
        Route::get('/{post}',          'show')->name('show');
        Route::patch('/{post}/like',   'like')->name('like');
        Route::patch('/{post}/unlike', 'unlike')->name('unlike');
    });
});