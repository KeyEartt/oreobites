<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Dashboard\StaffController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Services\SupabaseService;

// ===== Public =====
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::get('/success', [CheckoutController::class, 'success'])->name('success');
Route::get('/track', [TrackingController::class, 'index'])->name('track');

// ===== Auth =====
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// ===== API =====
Route::get('/api/products', function (SupabaseService $supabase) {
    return response()->json($supabase->fetchProducts());
});

Route::get('/api/order/{orderNumber}', function (string $orderNumber, SupabaseService $supabase) {
    $order = $supabase->findOrderByNumber($orderNumber);
    return response()->json($order ?: ['error' => 'Order not found'], $order ? 200 : 404);
});

Route::post('/api/create-payment', [PaymentController::class, 'create']);
Route::post('/api/webhook', [WebhookController::class, 'handle']);
Route::post('/api/track-order', [TrackingController::class, 'lookup']);

// ===== Customer (auth required) =====
Route::middleware(['require.customer'])->group(function () {
    Route::get('/my-orders', [CustomerController::class, 'myOrders'])->name('my-orders');
});

// ===== Staff =====
Route::middleware(['require.staff'])->group(function () {
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.dashboard');
    Route::get('/staff/queue', [StaffController::class, 'queue']);
    Route::post('/staff/update-order', [StaffController::class, 'updateOrder']);
});

// ===== Admin =====
Route::middleware(['require.admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/update-stock', [AdminController::class, 'updateStock']);
    Route::post('/admin/toggle-product', [AdminController::class, 'toggleProduct']);
});