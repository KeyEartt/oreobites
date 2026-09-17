<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(SupabaseService $supabase)
    {
        $products = $supabase->fetchAllProducts();
        $orders = $supabase->fetchAllOrders();

        // Quick stats
        $totalRevenue = collect($orders)
            ->where('payment_status', 'paid')
            ->sum('total');

        $totalOrders = count($orders);
        $paidOrders = collect($orders)->where('payment_status', 'paid')->count();

        return view('dashboard.admin', compact(
            'products',
            'orders',
            'totalRevenue',
            'totalOrders',
            'paidOrders'
        ));
    }

    public function updateStock(Request $request, SupabaseService $supabase)
    {
        $validated = $request->validate([
            'product_id' => 'required|string',
            'stock' => 'required|integer|min:0',
        ]);

        $updated = $supabase->updateProductStock(
            $validated['product_id'],
            $validated['stock']
        );

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Stock updated.' : 'Failed to update stock.',
        ]);
    }

    public function toggleProduct(Request $request, SupabaseService $supabase)
    {
        $validated = $request->validate([
            'product_id' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        $updated = $supabase->updateProduct($validated['product_id'], [
            'is_active' => $validated['is_active'],
        ]);

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Product updated.' : 'Failed to update product.',
        ]);
    }
}