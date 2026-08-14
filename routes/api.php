<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Provider\BookingController as ProviderBookingController;

Route::prefix('v1')->group(function () {
    Route::middleware([
        'auth:sanctum',
        'active',
        'verified',
        'throttle:api',
    ])->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::middleware('abilities:services:read')->group(function () {
            Route::get(
                '/services/{service}/slots',
                [ServiceController::class, 'slots']
            )->name('services.slots');

            Route::apiResource(
                'services',
                ServiceController::class
            )->only([
                'index',
                'show',
            ]);
        });

        Route::middleware('abilities:bookings:read')->group(function () {
            Route::get(
                '/bookings',
                [BookingController::class, 'index']
            )->name('bookings.index');

            Route::get(
                '/bookings/{booking}',
                [BookingController::class, 'show']
            )->name('bookings.show');
        });

        Route::middleware('abilities:bookings:create')->group(function () {
            Route::post(
                '/bookings',
                [BookingController::class, 'store']
            )->name('bookings.store');
        });

        Route::middleware('abilities:bookings:cancel')->group(function () {
            Route::patch(
                '/bookings/{booking}/cancel',
                [BookingController::class, 'cancel']
            )->name('bookings.cancel');
        });

        Route::middleware(
            'abilities:provider:manage-bookings'
        )->prefix('provider')->group(function () {
            Route::get(
                '/bookings',
                [ProviderBookingController::class, 'index']
            )->name('api.v1.provider.bookings.index');

            Route::patch(
                '/bookings/{booking}/status',
                [ProviderBookingController::class, 'updateStatus']
            )->name('api.v1.provider.bookings.status');
        });
    });
});