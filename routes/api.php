<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\CarouselController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\KontenController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\SystemStatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Tanpa Perlu Login)
|--------------------------------------------------------------------------
*/
Route::get('/ping', function () { return response()->json(['status' => 'ok', 'time' => microtime(true)]); });
Route::get('/home', [PublicController::class, 'home']);
Route::get('/konfigurasi', [KonfigurasiController::class, 'show']);
Route::get('/carousel', [CarouselController::class, 'index']);
Route::get('/kategori', [KategoriController::class, 'index']);
Route::get('/kategori/{id}', [KategoriController::class, 'show']);
Route::get('/konten', [KontenController::class, 'index']);
Route::get('/konten/{id}', [KontenController::class, 'show']);
Route::get('/system-stats', [SystemStatsController::class, 'index']);

// Otentikasi
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Wajib Header: Authorization: Bearer <TOKEN>)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Sesi Profil
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Manajemen User
    Route::apiResource('users', UsersController::class);

    // Manajemen Kategori
    Route::post('/kategori', [KategoriController::class, 'store']);
    Route::put('/kategori/{id}', [KategoriController::class, 'update']);
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);

    // Manajemen Konten (Published & Archived)
    Route::post('/konten', [KontenController::class, 'store']);
    Route::put('/konten/{id}', [KontenController::class, 'update']);
    Route::delete('/konten/{id}', [KontenController::class, 'destroy']);

    // Manajemen Carousel
    Route::post('/carousel', [CarouselController::class, 'store']);
    Route::get('/carousel/{id}', [CarouselController::class, 'show']);
    Route::put('/carousel/{id}', [CarouselController::class, 'update']);
    Route::delete('/carousel/{id}', [CarouselController::class, 'destroy']);

    // Manajemen Konfigurasi Website
    Route::match(['put', 'post'], '/konfigurasi', [KonfigurasiController::class, 'update']);

    // Log Aktivitas & Audit Trail (Khusus Admin)
    Route::get('/logs', [ActivityLogController::class, 'index']);
    Route::post('/logs/clear', [ActivityLogController::class, 'clear']);
});
