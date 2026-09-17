<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        return view('track');
    }

    public function lookup(Request $request, SupabaseService $supabase)
    {
        $validated = $request->validate([
            'order_number' => 'required|string',
        ]);

        $order = $supabase->findOrderByNumber(strtoupper(trim($validated['order_number'])));

        if (!$order) {
            return response()->json(['error' => 'Order not found. Check your order number.'], 404);
        }

        // Return only safe, non-sensitive fields
        return response()->json([
            'order_number' => $order['order_number'],
            'status' => $order['status'],
            'payment_status' => $order['payment_status'],
            'customer_name' => $order['customer_name'],
            'delivery_type' => $order['delivery_type'],
            'delivery_address' => $order['delivery_address'],
            'eta' => $order['eta'],
            'total' => $order['total'],
            'items' => $order['items'],
            'created_at' => $order['created_at'],
            'updated_at' => $order['updated_at'],
        ]);
    }
}