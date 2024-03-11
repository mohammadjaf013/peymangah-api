<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Auth\App\Http\Controllers\AuthController;

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


Route::prefix('/auth')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'login');
        Route::post('/register-otp', 'registerOtp');
        Route::post('/register-verify', 'registerVerify');
        Route::post('/register', 'register');
        Route::get('/data', 'data');
        Route::post('/forget-otp', 'forgetOtp');
        Route::post('/forget-verify', 'forgetVerify');
        Route::post('/forget-pass', 'forgetPass');


    });

});
