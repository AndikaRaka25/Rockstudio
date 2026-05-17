<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Renter\DashboardController as RenterDashboard;
use App\Http\Controllers\Renter\BookingController;
use App\Http\Controllers\Renter\StudioController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboard;
use App\Http\Controllers\Owner\FinanceController;
use App\Http\Controllers\Owner\InventoryController;
use App\Http\Controllers\Owner\BookingManageController;
use App\Http\Controllers\Owner\EventController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// === Public / Landing ===
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isOwner()
            ? redirect()->route('owner.dashboard')
            : redirect()->route('renter.dashboard');
    }
    return redirect()->route('login');
})->name('home');

// === Language Switch ===
Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'id'])) {
        session()->put('locale', $locale);
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
    }
    return redirect()->back();
})->name('locale.switch');

// === Theme Switch ===
Route::get('/theme/{theme}', function (string $theme) {
    if (in_array($theme, ['light', 'dark'])) {
        session()->put('theme', $theme);
        if (auth()->check()) {
            auth()->user()->update(['theme' => $theme]);
        }
    }
    return redirect()->back();
})->name('theme.switch');

// === Auth Routes ===
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// === Renter Routes ===
Route::middleware(['auth', 'role:renter'])->prefix('renter')->name('renter.')->group(function () {
    Route::get('/dashboard', [RenterDashboard::class, 'index'])->name('dashboard');
    Route::get('/booking/{studio?}', [BookingController::class, 'index'])->name('booking');
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('my-bookings');
    Route::get('/booking-detail/{booking}', [BookingController::class, 'show'])->name('booking.show');
    Route::get('/booking-detail/{booking}/pdf', [BookingController::class, 'downloadPdf'])->name('booking.pdf');
    Route::post('/booking-detail/{booking}/email', [BookingController::class, 'sendEmail'])->name('booking.email');
    Route::get('/studios', [StudioController::class, 'index'])->name('studio.index');
    Route::get('/studio/{studio}', [StudioController::class, 'show'])->name('studio.detail');
});

// === Owner Routes ===
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboard::class, 'index'])->name('dashboard');
    Route::resource('/finance', FinanceController::class)->except(['show']);
    Route::resource('/inventory', InventoryController::class)->except(['show']);
    Route::get('/bookings', [BookingManageController::class, 'index'])->name('bookings.index');
    Route::resource('/events', EventController::class)->except(['show']);
    Route::get('/schedule/{studio?}', [BookingManageController::class, 'schedule'])->name('schedule');
});

// === Payment Webhook (exclude from CSRF) ===
Route::post('/api/midtrans/webhook', [PaymentController::class, 'webhook'])->name('midtrans.webhook');
