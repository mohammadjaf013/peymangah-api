<?php

use Illuminate\Support\Facades\Route;
use Modules\Contract\App\Http\Controllers\CategoryController;
use Modules\Contract\App\Http\Controllers\ContractController;
use Modules\Contract\App\Http\Controllers\ContractsController;
use Modules\Contract\App\Http\Controllers\SignController;
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
Route::prefix('/contract')->group(function () {
    Route::controller(ContractController::class)->group(function () {
        Route::get('/banback', 'banback');
        Route::get('/pdf/{id}', 'pdf');

    });
});

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
            Route::post('/cr/{id}', 'cr');
            Route::post('/update/{id}', 'update');
            Route::post('/payment/{id}', 'payment');
            Route::post('/active/{id}', 'active');

        });
        Route::controller(ContractsController::class)->group(function () {
            Route::get('/list', 'list');

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

    Route::prefix('/sign')->group(function () {
            Route::controller(SignController::class)->group(function () {
                Route::post('/otp', 'otp');
                Route::post('/verify', 'verify');
                Route::get('/data/{token}', 'details');
                Route::post('/face/{token}', 'face');
                Route::post('/signature/{token}', 'signature');
            });
        Route::prefix('/check')->group(function () {
            Route::controller(SignController::class)->group(function () {
                Route::get('/{id}/{code}', 'check');
            });
        });
    });
