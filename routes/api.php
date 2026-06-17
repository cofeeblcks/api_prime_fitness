<?php

use App\Http\Controllers\API\AccessControlController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LookupController;
use App\Http\Controllers\API\MemberController;
use App\Http\Controllers\API\PasswordRecoveryController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\SuscriptionController;
use App\Http\Controllers\API\TrainerController;
use App\Http\Controllers\API\UserController;
use App\Models\User;
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
    Route::get('plans', 'plans');
    Route::get('suscription-types', 'suscriptionTypes');
});

Route::bind('member', fn (string $value) => User::query()->members()->findOrFail($value));
Route::bind('trainer', fn (string $value) => User::query()->trainers()->findOrFail($value));

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class)->middleware('module.access:users');
    Route::apiResource('members', MemberController::class)->middleware('module.access:members');
    Route::apiResource('trainers', TrainerController::class)->middleware('module.access:trainers');
    Route::apiResource('plans', PlanController::class)->middleware('module.access:plans');
    Route::apiResource('payments', SuscriptionController::class)
        ->parameters(['payments' => 'suscription'])
        ->middleware('module.access:payments');
    Route::apiResource('access-control', AccessControlController::class)
        ->only(['index', 'store'])
        ->parameters(['access-control' => 'accessControl'])
        ->middleware('module.access:access-control');
});
