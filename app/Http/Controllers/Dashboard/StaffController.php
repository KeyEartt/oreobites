<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(SupabaseService $supabase)
    {
        $orders            = $supabase->fetchActiveOrders();
        $pendingInPerson   = $supabase->fetchPendingInPersonOrders();

        return view('dashboard.staff', compact('orders', 'pendingInPerson'));
    }

    public function queue(SupabaseService $supabase)
    {
        $orders          = $supabase->fetchActiveOrders();
        $pendingInPerson = $supabase->fetchPendingInPersonOrders();

        return response()->json([
            'active'    => view('dashboard.partials.order-queue', ['orders' => $orders])->render(),
            'pending'   => view('dashboard.partials.order-queue', ['orders' => $pendingInPerson])->render(),
            'counts'    => [
                'paid'      => collect($orders)->where('status', 'paid')->count(),
                'preparing' => collect($orders)->where('status', 'preparing')->count(),
                'ready'     => collect($orders)->where('status', 'ready')->count(),
                'pending'   => count($pendingInPerson),
            ],
        ]);
    }

    public function updateOrder(Request $request, SupabaseService $supabase)
    {
        $validated = $request->validate([
            'order_id' => 'required|string',
            'status'   => 'required|in:paid,preparing,ready,picked_up,cancelled',
        ]);

        $updated = $supabase->updateOrder($validated['order_id'], [
            'status'     => $validated['status'],
            'updated_at' => now()->utc()->format('Y-m-d\TH:i:s\Z'),
        ]);

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Order updated.' : 'Failed to update order.',
        ]);
    }

    /**
     * Confirm that a pending in-person order has been paid at the kiosk.
     * Sets payment_status=paid AND status=paid in one atomic-ish update.
     */
    public function markPaid(Request $request, SupabaseService $supabase)
    {
        $validated = $request->validate([
            'order_id' => 'required|string',
        ]);

        $updated = $supabase->updateOrder($validated['order_id'], [
            'payment_status' => 'paid',
            'status'         => 'paid',
            'updated_at'     => now()->utc()->format('Y-m-d\TH:i:s\Z'),
        ]);

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Payment confirmed.' : 'Failed to confirm payment.',
        ]);
    }
}