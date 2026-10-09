<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Dashboard\StaffController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Services\SupabaseService;

// ===== Public =====
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/success', [CheckoutController::class, 'success'])->name('success');
Route::get('/track', [TrackingController::class, 'index'])->name('track');

Route::get('/terms', [LegalController::class, 'terms'])->name('terms');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('privacy');

// ===== Auth =====
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])
    ->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');
    
// ===== Password Reset =====
Route::get('/forgot-password', [ForgotPasswordController::class, 'showRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->middleware('throttle:5,15')->name('password.email');

Route::get('/forgot-password/verify', [ForgotPasswordController::class, 'showVerifyForm'])->name('password.verify.form');
Route::post('/forgot-password/verify', [ForgotPasswordController::class, 'verifyOtp'])->middleware('throttle:10,15')->name('password.verify');

Route::get('/forgot-password/reset', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])->middleware('throttle:5,15')->name('password.reset');

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

// ===== Authenticated =====
Route::middleware(['require.customer'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::get('/my-orders', [CustomerController::class, 'myOrders'])->name('my-orders');
});

// ===== Staff =====
Route::middleware(['require.staff'])->group(function () {
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.dashboard');
    Route::get('/staff/queue', [StaffController::class, 'queue']);
    Route::post('/staff/update-order', [StaffController::class, 'updateOrder']);
    Route::post('/staff/mark-paid', [StaffController::class, 'markPaid']);
});

// ===== Admin =====
Route::middleware(['require.admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::post('/admin/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::post('/admin/products/update', [AdminController::class, 'updateProduct'])->name('admin.products.update');
    Route::post('/admin/products/delete', [AdminController::class, 'deleteProduct'])->name('admin.products.delete');

    Route::post('/admin/update-stock', [AdminController::class, 'updateStock']);
    Route::post('/admin/toggle-product', [AdminController::class, 'toggleProduct']);

    // Admin reuses the staff markPaid via separate route for cleanliness
    Route::post('/admin/mark-paid', [StaffController::class, 'markPaid']);
});