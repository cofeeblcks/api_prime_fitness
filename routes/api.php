<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LookupController;
use App\Http\Controllers\API\PasswordRecoveryController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
	Route::controller(AuthController::class)->group(function () {
		Route::post('login', 'login');
	});

	Route::controller(PasswordRecoveryController::class)->group(function () {
        Route::post('password/request-reset', 'requestReset');
        Route::post('password/reset', 'resetPassword');
    });
});

Route::prefix('lookups')->controller(LookupController::class)->group(function () {
	Route::get('identification-types', 'identificationTypes');
	Route::get('roles', 'roles');
});

Route::middleware('auth:sanctum')->group(function () {
	Route::apiResource('users', UserController::class)->middleware('module.access:users');
});
