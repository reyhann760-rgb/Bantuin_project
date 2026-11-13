<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceOfferController;
use App\Http\Controllers\ServiceRequestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');


// ===== Preview Frontend 1 (data dummy, bisa dibuka tanpa login) =====
Route::get('/beranda', [PageController::class, 'home'])->name('beranda');

Route::get('/butuh-jasa', [PageController::class, 'butuhIndex'])->name('butuh.index');
Route::get('/butuh-jasa/{id}', [PageController::class, 'butuhShow'])->name('butuh.show');

Route::get('/menyediakan-jasa', [PageController::class, 'sediaIndex'])->name('sedia.index');
Route::get('/menyediakan-jasa/{id}', [PageController::class, 'sediaShow'])->name('sedia.show');


// Route untuk pengguna yang belum login
Route::middleware('guest')->group(function () {

    // Registrasi
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');

    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');

});


// Route untuk pengguna yang sudah login
Route::middleware('auth')->group(function () {

    // Pengalihan dashboard berdasarkan role
    Route::get('/dashboard', [AuthController::class, 'dashboard'])
        ->name('dashboard');

    // Dashboard Customer
    Route::view('/customer/dashboard', 'dashboard')
        ->middleware('role:customer')
        ->name('customer.dashboard');

    // Dashboard Penyedia Jasa
    Route::view('/provider/dashboard', 'dashboard')
        ->middleware('role:provider')
        ->name('provider.dashboard');

    // ===== Butuh Jasa (permintaan dari customer) =====
    Route::get('/service-requests', [ServiceRequestController::class, 'index'])
        ->name('service-requests.index');

    // Khusus customer. Harus di atas route {serviceRequest}
    Route::middleware('role:customer')->group(function () {
        Route::get('/service-requests/mine', [ServiceRequestController::class, 'mine'])
            ->name('service-requests.mine');
        Route::get('/service-requests/create', [ServiceRequestController::class, 'create'])
            ->name('service-requests.create');
        Route::post('/service-requests', [ServiceRequestController::class, 'store'])
            ->name('service-requests.store');
        Route::get('/service-requests/{serviceRequest}/edit', [ServiceRequestController::class, 'edit'])
            ->name('service-requests.edit');
        Route::put('/service-requests/{serviceRequest}', [ServiceRequestController::class, 'update'])
            ->name('service-requests.update');
        Route::delete('/service-requests/{serviceRequest}', [ServiceRequestController::class, 'destroy'])
            ->name('service-requests.destroy');
    });

    Route::get('/service-requests/{serviceRequest}', [ServiceRequestController::class, 'show'])
        ->name('service-requests.show');

    // ===== Menyediakan Jasa (penawaran dari provider) =====
    Route::get('/service-offers', [ServiceOfferController::class, 'index'])
        ->name('service-offers.index');

    // Khusus provider. Harus di atas route {serviceOffer}
    Route::middleware('role:provider')->group(function () {
        Route::get('/service-offers/mine', [ServiceOfferController::class, 'mine'])
            ->name('service-offers.mine');
        Route::get('/service-offers/create', [ServiceOfferController::class, 'create'])
            ->name('service-offers.create');
        Route::post('/service-offers', [ServiceOfferController::class, 'store'])
            ->name('service-offers.store');
        Route::get('/service-offers/{serviceOffer}/edit', [ServiceOfferController::class, 'edit'])
            ->name('service-offers.edit');
        Route::put('/service-offers/{serviceOffer}', [ServiceOfferController::class, 'update'])
            ->name('service-offers.update');
        Route::delete('/service-offers/{serviceOffer}', [ServiceOfferController::class, 'destroy'])
            ->name('service-offers.destroy');
    });

    Route::get('/service-offers/{serviceOffer}', [ServiceOfferController::class, 'show'])
        ->name('service-offers.show');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

});