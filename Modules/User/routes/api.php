<?php

use Illuminate\Support\Facades\Route;
use Modules\User\App\Http\Controllers\ChangePassController;
use Modules\User\App\Http\Controllers\TransactionsController;
use Modules\User\App\Http\Controllers\UserController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/
Route::middleware('auth:api')->group(function () {
    Route::prefix('/user')->group(function () {
        Route::controller(ChangePassController::class)->group(function () {
            Route::post('/change-pass', 'index');
        });

        Route::controller(UserController::class)->group(function () {
            Route::post('/update-data', 'updateData');
        });

    });

    Route::prefix('/transactions')->group(function () {
        Route::controller(TransactionsController::class)->group(function () {
            Route::get('/', 'index');
        });
    });
});
