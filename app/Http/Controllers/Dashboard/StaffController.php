<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(SupabaseService $supabase)
    {
        $orders = $supabase->fetchActiveOrders();
        return view('dashboard.staff', compact('orders'));
    }

    /**
     * Returns only the order queue partial (for AJAX polling)
     */
    public function queue(SupabaseService $supabase)
    {
        $orders = $supabase->fetchActiveOrders();
        return view('dashboard.partials.order-queue', compact('orders'));
    }

    public function updateOrder(Request $request, SupabaseService $supabase)
    {
        $validated = $request->validate([
            'order_id' => 'required|string',
            'status' => 'required|in:paid,preparing,ready,picked_up,cancelled',
        ]);

        $updated = $supabase->updateOrder($validated['order_id'], [
            'status' => $validated['status'],
            'updated_at' => now()->toIso8601String(),
        ]);

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Order updated.' : 'Failed to update order.',
        ]);
    }
}