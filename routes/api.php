<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\ServiceOfferController;
use App\Http\Controllers\Api\ServiceRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Laravel Sanctum) - Backend 3
|--------------------------------------------------------------------------
*/

// Public Authentication
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Public Listing endpoints
Route::get('/service-requests', [ServiceRequestController::class, 'index'])->name('api.requests.index');
Route::get('/service-requests/{serviceRequest}', [ServiceRequestController::class, 'show'])->name('api.requests.show');

Route::get('/service-offers', [ServiceOfferController::class, 'index'])->name('api.offers.index');
Route::get('/service-offers/{serviceOffer}', [ServiceOfferController::class, 'show'])->name('api.offers.show');

// Protected Routes (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Current User & Logout
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // Create listings
    Route::post('/service-requests', [ServiceRequestController::class, 'store'])->name('api.requests.store');
    Route::post('/service-offers', [ServiceOfferController::class, 'store'])->name('api.offers.store');

    // Conversations & Messages
    Route::get('/conversations', [ConversationController::class, 'index'])->name('api.conversations.index');
    Route::post('/conversations', [ConversationController::class, 'store'])->name('api.conversations.store');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('api.conversations.show');
    Route::get('/conversations/{conversation}/messages', [ConversationController::class, 'messages'])->name('api.conversations.messages');
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage'])->name('api.conversations.send');
});

