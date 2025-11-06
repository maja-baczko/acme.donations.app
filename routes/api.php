<?php

use App\Http\Controllers\AuthController;
use App\Modules\Administration\Http\Controllers\AuditLogController;
use App\Modules\Administration\Http\Controllers\SystemSettingController;
use App\Modules\Campaign\Http\Controllers\CampaignController;
use App\Modules\Campaign\Http\Controllers\CategoryController;
use App\Modules\Donation\Http\Controllers\DonationController;
use App\Modules\Media\Http\Controllers\ImageController;
use App\Modules\Payment\Http\Controllers\PaymentController;
use App\Modules\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    /* --------------------------------------------------------------------------
      * Public Routes (No Authentication Required)
      * -------------------------------------------------------------------------*/
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    // Public campaigns (for browsing without login)
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index.public');
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show.public');

    // Public categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index.public');

    // Public system settings
    Route::get('/settings/public', [SystemSettingController::class, 'public'])->name('settings.public');


    /* --------------------------------------------------------------------------
     * Protected Routes (Authentication Required)
     * -------------------------------------------------------------------------*/
    Route::middleware('auth:sanctum')->group(function () {
        // Authentication
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');

        // Users --------------------------------------------------------------------------
        Route::middleware('permission:view users')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        });

        Route::middleware('permission:create users')->group(function () {
            Route::post('/users', [UserController::class, 'create'])->name('users.create');
        });

        Route::middleware('permission:edit users')->group(function () {
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        });

        Route::middleware('permission:delete users')->group(function () {
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        // Campaign --------------------------------------------------------------------------
        Route::middleware('permission:create campaigns')->group(function () {
            Route::post('/campaigns', [CampaignController::class, 'create'])->name('campaigns.create');
        });

        Route::middleware('permission:edit campaigns')->group(function () {
            Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
        });

        Route::middleware('permission:delete campaigns')->group(function () {
            Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');
        });

        // Category
        Route::middleware('permission:edit campaigns')->group(function () {
            Route::post('/categories', [CategoryController::class, 'create'])->name('categories.create');
            Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        });

        // Donation --------------------------------------------------------------------------
        Route::middleware('permission:view donations')->group(function () {
            Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
            Route::get('/donations/{donation}', [DonationController::class, 'show'])->name('donations.show');
        });

        Route::middleware('permission:create donations')->group(function () {
            Route::post('/donations', [DonationController::class, 'create'])->name('donations.create');
        });

        Route::middleware('permission:edit donations')->group(function () {
            Route::put('/donations/{donation}', [DonationController::class, 'update'])->name('donations.update');
        });

        Route::middleware('permission:delete donations')->group(function () {
            Route::delete('/donations/{donation}', [DonationController::class, 'destroy'])->name('donations.destroy');
        });

        // Payment --------------------------------------------------------------------------
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

        // Image --------------------------------------------------------------------------
        Route::apiResource('images', ImageController::class);

        /* --------------------------------------------------------------------------
         * Admin only
         * -------------------------------------------------------------------------*/
        // System Settings
        Route::middleware('permission:view system settings')->group(function () {
            Route::get('/settings', [SystemSettingController::class, 'index'])->name('settings.index');
            Route::get('/settings/{systemSetting}', [SystemSettingController::class, 'show'])->name('settings.show');
        });

        Route::middleware('permission:edit system settings')->group(function () {
            Route::post('/settings', [SystemSettingController::class, 'create'])->name('settings.create');
            Route::put('/settings/{systemSetting}', [SystemSettingController::class, 'update'])->name('settings.update');
            Route::delete('/settings/{systemSetting}', [SystemSettingController::class, 'destroy'])->name('settings.destroy');
        });

        // Audit Logs --------------------------------------------------------------------------
        Route::middleware('permission:view audit logs')->group(function () {
            Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
            Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
        });
    });
});
