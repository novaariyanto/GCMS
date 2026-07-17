<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ComplaintController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\MasterDataController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('otp/send', [AuthController::class, 'sendOtp']);
        Route::post('otp/verify', [AuthController::class, 'verifyOtp']);

        Route::middleware('jwt.auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    Route::middleware('jwt.auth')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('complaints', ComplaintController::class);
        Route::get('complaints/{id}/timeline', [ComplaintController::class, 'timeline']);
        Route::get('complaints/{id}/histories', [ComplaintController::class, 'histories']);
        Route::post('complaints/{id}/transition', [ComplaintController::class, 'transition']);

        Route::get('workflows', [WorkflowController::class, 'index']);
        Route::get('workflows/{id}', [WorkflowController::class, 'show']);

        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/{id}/read', [NotificationController::class, 'markRead']);

        Route::get('dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('reports/complaints', [ReportController::class, 'complaints']);

        Route::prefix('master')->group(function () {
            Route::get('categories', [MasterDataController::class, 'categories']);
            Route::get('priorities', [MasterDataController::class, 'priorities']);
            Route::get('statuses', [MasterDataController::class, 'statuses']);
            Route::get('opds', [MasterDataController::class, 'opds']);
            Route::get('regions', [MasterDataController::class, 'regions']);
            Route::get('districts', [MasterDataController::class, 'districts']);
            Route::get('villages', [MasterDataController::class, 'villages']);
        });

        Route::get('search', [SearchController::class, 'index']);
    });
});
