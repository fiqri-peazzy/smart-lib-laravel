<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DigitalLibraryController;
use App\Http\Controllers\UserDashboardController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Books Routes (Public - dapat browse tanpa login)
Route::prefix('books')->name('books.')->group(function () {
    Route::get('/', [BookController::class, 'index'])->name('index');
    Route::get('/{book}', [BookController::class, 'show'])->name('show');
    Route::get('/category/{category}', [BookController::class, 'byCategory'])->name('category');
});

// Digital Library Routes (Public browse, download butuh login)
Route::prefix('digital')->name('digital.')->group(function () {
    Route::get('/', [DigitalLibraryController::class, 'index'])->name('index');
    Route::get('/{collection}', [DigitalLibraryController::class, 'show'])->name('show');

    // Protected routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/{collection}/read', [DigitalLibraryController::class, 'read'])->name('read');
        Route::get('/{collection}/download', [DigitalLibraryController::class, 'download'])->name('download');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'user.role'])->group(function () {

    // User Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // My Loans
    Route::prefix('my-loans')->name('loans.')->group(function () {
        Route::get('/', [UserDashboardController::class, 'loans'])->name('my-loans');
        Route::post('/{loan}/extend', [UserDashboardController::class, 'extendLoan'])->name('extend');
    });

    // My Bookings
    Route::prefix('my-bookings')->name('bookings.')->group(function () {
        Route::get('/', [UserDashboardController::class, 'bookings'])->name('my-bookings');
        Route::post('/create', [UserDashboardController::class, 'createBooking'])->name('create');
        Route::delete('/{booking}', [UserDashboardController::class, 'cancelBooking'])->name('cancel');
    });

    // Payment (Fines)
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/', [UserDashboardController::class, 'payment'])->name('index');
        Route::post('/process', [UserDashboardController::class, 'processPayment'])->name('process');
    });

    // Profile
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Already handled by Filament)
|--------------------------------------------------------------------------
*/
// /admin/* handled by Filament

/*
|--------------------------------------------------------------------------
| Auth Routes (Laravel Breeze/Jetstream)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
