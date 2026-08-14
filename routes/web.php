<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Provider\BusinessProfile;
use App\Livewire\Provider\ServiceManager;
use App\Livewire\Provider\AvailabilityManager;
use App\Livewire\Customer\ServiceCatalog;
use App\Livewire\Customer\ServiceDetails;
use App\Livewire\Customer\BookingCreator;
use App\Livewire\Customer\BookingList;
use App\Livewire\Customer\ReviewCreator;
use App\Livewire\Provider\BookingManager;
use App\Livewire\Admin\BusinessManager;
use App\Livewire\Admin\UserManager;
use App\Livewire\Admin\BookingMonitor;
use App\Livewire\Admin\ActivityLogViewer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'active',
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $user = User::query()->findOrFail(Auth::id());

        if ($user->isCustomer()) {
            return redirect()->route('customer.services.index');
        }

        if ($user->isProvider()) {
            return redirect()->route('provider.services.index');
        }

        return view('dashboard');
    })->name('dashboard');

    Route::middleware('role:provider')
        ->prefix('provider')
        ->name('provider.')
        ->group(function () {
            Route::get('/business', BusinessProfile::class)
                ->name('business.profile');

            Route::get('/services', ServiceManager::class)
            ->name('services.index');

            Route::get('/availability', AvailabilityManager::class)
            ->name('availability.index');

             Route::get('/bookings', BookingManager::class)
            ->name('bookings.index');
        });

    Route::middleware('role:customer')
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::get('/services', ServiceCatalog::class)
            ->name('services.index');

        Route::get('/services/{service}', ServiceDetails::class)
            ->name('services.show');

        Route::get('/services/{service}/book/{slot}', BookingCreator::class)
            ->name('bookings.create');

        Route::get('/bookings', BookingList::class)
            ->name('bookings.index');

        Route::get('/bookings/{booking}/review', ReviewCreator::class)
            ->name('bookings.review');
       });

       Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
         ->group(function () {
        Route::get('/businesses', BusinessManager::class)
            ->name('businesses.index');

             Route::get('/users', UserManager::class)
            ->name('users.index');

            Route::get('/bookings', BookingMonitor::class)
            ->name('bookings.index');

            Route::get('/activity-logs', ActivityLogViewer::class)
            ->name('activity.index');
       });
});