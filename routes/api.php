<?php

use App\Http\Controllers\API\AccessControlController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\LookupController;
use App\Http\Controllers\API\MeController;
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
    Route::get('link-types', 'linkTypes');
    Route::get('contact-statuses', 'contactStatuses');
});

Route::bind('member', fn (string $value) => User::query()->members()->findOrFail($value));
Route::bind('trainer', fn (string $value) => User::query()->trainers()->findOrFail($value));

Route::get('company/{company}', [CompanyController::class, 'show']);

Route::post('contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('companies', CompanyController::class)
        ->except(['show'])
        ->middleware('module.access:company');
    Route::apiResource('contacts', ContactController::class)
        ->except(['store'])
        ->middleware('module.access:contacts');
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

    Route::prefix('me')->group(function () {
        Route::get('/', [MeController::class, 'show']);
        Route::get('subscriptions', [MeController::class, 'subscriptions']);
        Route::get('weight-controls', [MeController::class, 'weightControls']);
        Route::post('weight-controls', [MeController::class, 'storeWeightControl']);
    });
});
