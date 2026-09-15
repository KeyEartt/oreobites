<?php

use Illuminate\Support\Facades\Route;
use App\Services\SupabaseService;

Route::get('/', function () {
    return view('welcome');
});

// ⬇️ ADD THIS AT THE BOTTOM ⬇️
Route::get('/test-supabase', function (SupabaseService $supabase) {
    $result = $supabase->testConnection();
    
    if ($result['success']) {
        $products = $supabase->fetchProducts();
        return response()->json([
            'connection' => '✅ Connected!',
            'product_count' => count($products),
            'products' => $products,
        ]);
    } else {
        return response()->json([
            'connection' => '❌ Failed!',
            'error' => $result['error'],
        ]);
    }
});