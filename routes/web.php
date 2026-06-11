<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TryOnController;
use App\Http\Controllers\SkinAnalysisController;
use App\Http\Controllers\Api\SkinRecommendationController;

Route::get('/', [HomeController::class, 'index'])->name('home');

/* =========================
   AUTH (GUEST ONLY)
========================= */
Route::middleware('guest')->group(function () {
    Route::get('/daftar', fn () => view('daftar'))->name('daftar.show');
    Route::post('/daftar', [RegisterController::class, 'store'])->name('daftar.store');

    Route::get('/login', fn () => view('login'))->name('login');
    Route::post('/login', [LoginController::class, 'process'])
        ->middleware('throttle:10,1') // anti brute force
        ->name('login.process');
});

/* =========================
   LOGOUT (AUTH ONLY)
========================= */
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/* =========================
   PUBLIC ROUTES
========================= */
Route::get('/produk', [ProductController::class, 'index'])->name('produk');
Route::get('/produk/{id}', [ProductController::class, 'show'])->name('produk.show');

Route::get('/virtual_tryon', [TryOnController::class, 'index'])->name('tryon');
Route::get('/produk/{product}/tryon/{shade}', [TryOnController::class, 'show'])->name('tryon.show');

/* =========================
   CART (PUBLIC VIEW)
   - keranjang boleh dibuka guest
========================= */
Route::get('/keranjang', [CartController::class, 'index'])->name('keranjang');

/* =========================
   AUTH-ONLY ROUTES
========================= */
Route::middleware('auth')->group(function () {

    Route::get('/rekomendasi', [RecommendationController::class, 'index'])
        ->name('rekomendasi');

    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile');

    Route::post('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::get('/pesanan-saya', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/pesanan/{order}', [OrderController::class, 'show'])
        ->name('orders.show');

    // checkout harus login
    Route::get('/checkout', [CheckoutController::class, 'show'])
        ->name('checkout.show');

    Route::post('/checkout', [CheckoutController::class, 'process'])
        ->name('checkout.process');

    // cart actions harus login
    Route::post('/keranjang/tambah', [CartController::class, 'add'])
        ->name('keranjang.tambah');

    Route::post('/keranjang/update', [CartController::class, 'update'])
        ->name('keranjang.update');

    Route::post('/keranjang/hapus', [CartController::class, 'remove'])
        ->name('keranjang.hapus');

    /* =========================
       SKIN ANALYSIS (FITUR BARU)
    ========================= */
    Route::get('/skin-analysis', [SkinAnalysisController::class, 'index'])
        ->name('skin.analysis');

    // API endpoint: terima hasil analisis AI -> kembalikan rekomendasi produk (JSON)
    Route::post('/api/skin-recommend', [SkinRecommendationController::class, 'recommend'])
        ->name('api.skin.recommend');

    // API endpoint: simpan hasil analisis ke profil user
    Route::post('/api/skin-save', [SkinRecommendationController::class, 'saveProfile'])
        ->name('api.skin.save');
});

/* =========================
   HEX PICKER (DEV)
========================= */
Route::get('/admin/hex-picker', function () {
    return view('filament.hex-picker-window');
})->name('filament.hex-picker');