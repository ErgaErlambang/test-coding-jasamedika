<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\TransactionController;

// Guess
Route::middleware('guest')->group(function() {
    Route::controller(AuthController::class)->group(function() {
        Route::get('/', 'login')->name('auth.login');
        Route::get('/register', 'register')->name('auth.register');
        Route::post('/signin', 'signin')->name('auth.signin');
        Route::post('/signup', 'signup')->name('auth.signup');
    });
});

// Auth
Route::middleware('auth')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::prefix('admin')->group(function() {
        Route::controller(DashboardController::class)->group(function() {
            Route::get('/', function() {return redirect()->route('dashboard');});
            Route::get('/dashboard', 'index')->name('dashboard');
        });

        Route::middleware('admin')->group(function () {
            Route::controller(UserController::class)->prefix('users')->group(function() {
                Route::get('/', 'index')->name('user.index');
                Route::post('/destroy/{id}', 'destroy')->name('user.destroy');
            });
    
            Route::controller(RolesController::class)->prefix('roles')->group(function() {
                Route::get('/', 'index')->name('roles.index');
            });            
        });

        Route::controller(VehicleController::class)->prefix('vehicles')->group(function() {
            Route::get('/', 'index')->name('vehicle.index');
            Route::get('/detail/{slug}', 'show')->name('vehicle.show');
            Route::post('/booking/{slug}', 'booking')->name('vehicle.booking');
            Route::post('/check-status', 'checkstatus')->name('vehicle.checkstatus');

            Route::middleware('admin')->group(function () {
                Route::get('/create', 'create')->name('vehicle.create');
                Route::post('/store', 'store')->name('vehicle.store');
                Route::get('/edit/{id}', 'edit')->name('vehicle.edit');
                Route::post('/update/{id}', 'update')->name('vehicle.update');
                Route::post('/destroy/{id}', 'destroy')->name('vehicle.destroy');
            });
        });

        Route::controller(TransactionController::class)->prefix('transaction')->group(function() {
            Route::get('/', 'index')->name('transaction.index');
            Route::get('/detail/{id}', 'show')->name('transaction.show');
            Route::post('/update-status/{id}', 'updatestatus')->name('transaction.updateStatus');
            Route::post('/return', 'return')->name('transaction.return');
            Route::get('/check-plate', 'checkplate')->name('transaction.checkPlate');
        });
    });
});