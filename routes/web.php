<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceOfferController;
use App\Http\Controllers\ServiceRequestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Halaman utama (Database Nyata)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/beranda', [HomeController::class, 'index'])->name('beranda');

// ===== Butuh Jasa (Publik / Database Nyata) =====
Route::get('/butuh-jasa', [ServiceRequestController::class, 'index'])->name('butuh.index');
Route::get('/service-requests', [ServiceRequestController::class, 'index'])->name('service-requests.index');

// ===== Menyediakan Jasa (Publik / Database Nyata) =====
Route::get('/menyediakan-jasa', [ServiceOfferController::class, 'index'])->name('sedia.index');
Route::get('/service-offers', [ServiceOfferController::class, 'index'])->name('service-offers.index');



// ===== Pemisahan Route Autentikasi (routes/auth.php) =====
require __DIR__.'/auth.php';

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
    });

    // Hapus kebutuhan jasa (customer pemilik atau admin)
    Route::middleware('role:customer,admin')->group(function () {
        Route::delete('/service-requests/{serviceRequest}', [ServiceRequestController::class, 'destroy'])
            ->name('service-requests.destroy');
    });

    // ===== Menyediakan Jasa (penawaran dari provider) =====
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
    });

    // Hapus penawaran jasa (provider pemilik atau admin)
    Route::middleware('role:provider,admin')->group(function () {
        Route::delete('/service-offers/{serviceOffer}', [ServiceOfferController::class, 'destroy'])
            ->name('service-offers.destroy');
    });

    // ===== Chat & Percakapan (Backend 3) =====
    Route::get('/chat', [ConversationController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ConversationController::class, 'store'])->name('chat.store');
    Route::get('/chat/{conversation}', [ConversationController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/pesan', [ConversationController::class, 'sendMessage'])
        ->middleware('throttle:30,1')
        ->name('chat.send');
    Route::get('/chat/{conversation}/pesan', [ConversationController::class, 'messages'])->name('chat.messages');
    Route::post('/chat/{conversation}/selesai', [ConversationController::class, 'complete'])->name('chat.complete');
    Route::post('/chat/{conversation}/ulasan', [ReviewController::class, 'store'])->name('chat.review');

    // ===== Notifikasi (Backend 3) =====
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifikasi/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifikasi/baca-semua', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // ===== Profil Pengguna =====
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // ===== Manajemen Kategori Admin =====
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/kategori', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/kategori', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/kategori/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/kategori/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    });

});

// Profil Publik Pengguna (dapat dilihat publik/tamu)
Route::get('/pengguna/{user}', [ProfileController::class, 'show'])->name('profile.show');

// Detail Listing Publik (dapat dilihat publik/tamu)
Route::get('/service-requests/{serviceRequest}', [ServiceRequestController::class, 'show'])->name('service-requests.show');
Route::get('/butuh-jasa/{serviceRequest}', [ServiceRequestController::class, 'show'])->name('butuh.show');

Route::get('/service-offers/{serviceOffer}', [ServiceOfferController::class, 'show'])->name('service-offers.show');
Route::get('/menyediakan-jasa/{serviceOffer}', [ServiceOfferController::class, 'show'])->name('sedia.show');

// Global Route Constraints untuk parameter ID numerik
Route::pattern('id', '[0-9]+');
Route::pattern('serviceRequest', '[0-9]+');
Route::pattern('serviceOffer', '[0-9]+');
Route::pattern('conversation', '[0-9]+');
Route::pattern('category', '[0-9]+');
Route::pattern('user', '[0-9]+');


