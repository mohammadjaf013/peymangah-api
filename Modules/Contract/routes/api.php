<?php

use Illuminate\Support\Facades\Route;
use Modules\Contract\App\Http\Controllers\CategoryController;
use Modules\Contract\App\Http\Controllers\ContractController;
use Modules\Contract\App\Http\Controllers\UserController;

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
    Route::prefix('/contract')->group(function () {
        Route::prefix('/category')->group(function () {
            Route::controller(CategoryController::class)->group(function () {
                Route::get('/', 'index');
            });
        });

        Route::controller(ContractController::class)->group(function () {
            Route::post('/create', 'create');
            Route::get('/details/{id}', 'details');
        });


        Route::prefix('/user')->group(function () {
            Route::controller(UserController::class)->group(function () {
                Route::post('/add', 'add');
                Route::get('/list/{id}', 'list');
                Route::post('/remove/{id}', 'remove');
            });
        });


    });
});
