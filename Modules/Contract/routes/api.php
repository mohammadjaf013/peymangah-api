<?php

use Illuminate\Support\Facades\Route;
use Modules\Contract\App\Http\Controllers\CategoryController;
use Modules\Contract\App\Http\Controllers\ContractController;
use Modules\Contract\App\Http\Controllers\ContractsController;
use Modules\Contract\App\Http\Controllers\DashboardController;
use Modules\Contract\App\Http\Controllers\ReceiptController;
use Modules\Contract\App\Http\Controllers\SignController;
use Modules\Contract\App\Http\Controllers\SignReceiptController;
use Modules\Contract\App\Http\Controllers\UserController;
use Modules\Contract\App\Http\Controllers\UserReceiptController;

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
Route::prefix('/receipt')->group(function () {
    Route::controller(ReceiptController::class)->group(function () {
        Route::get('/banback', 'banback');
        Route::get('/pdf/{id}', 'pdf');

    });
});


Route::middleware('auth:api')->group(function () {

    Route::prefix('/dashboard')->group(function () {
        Route::controller(DashboardController::class)->group(function () {
            Route::get('/', 'index');
        });
    });


    Route::prefix('/receipt')->group(function () {


        Route::controller(ReceiptController::class)->group(function () {
            Route::post('/create', 'create');
            Route::get('/details/{id}', 'details');
            Route::get('/list', 'list');
            Route::post('/update/{id}', 'update');
            Route::post('/payment/{id}', 'payment');
            Route::post('/active/{id}', 'active');
            Route::post('/sendsms/{id}', 'sendsms');

        });

        Route::prefix('/user')->group(function () {
            Route::controller(UserReceiptController::class)->group(function () {
                Route::post('/add', 'add');
                Route::get('/list/{id}', 'list');
                Route::post('/remove/{id}', 'remove');
            });
        });


    });

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
            Route::post('/sendsms/{id}', 'sendsms');

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
    Route::controller(SignReceiptController::class)->group(function () {
        Route::post('/otp-receipt', 'otp');
        Route::post('/verify-receipt', 'verify');
        Route::get('/data-receipt/{token}', 'details');
        Route::post('/face-receipt/{token}', 'face');
        Route::post('/signature-receipt/{token}', 'signature');
    });
    Route::prefix('/check')->group(function () {
        Route::controller(SignController::class)->group(function () {
            Route::get('/{id}/{code}', 'check');
        });
    });
    Route::prefix('/check-receipt')->group(function () {
        Route::controller(SignReceiptController::class)->group(function () {
            Route::get('/{id}/{code}', 'check');
        });
    });
});
