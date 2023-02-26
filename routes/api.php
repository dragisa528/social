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
        Route::get('/',                'index')->name('index');
        Route::post('/',               'store')->name('store');
        Route::put('/{user}',          'update')->name('store');
        Route::get('/{user}/follow',   'follow')->name('follow');
        Route::get('/{user}/unfollow', 'unfollow')->name('unfollow');
    });
    
    Route::controller(PostController::class)
    ->prefix('posts')
    ->name('posts.')
    ->group(function () {
        Route::get('/',                'index')->name('index');
        Route::post('/',               'store')->name('store');
        Route::put('/{post}',          'update')->name('store');
        Route::get('/{post}/follow',   'follow')->name('follow');
        Route::get('/{post}/unfollow', 'unfollow')->name('unfollow');
    });
});