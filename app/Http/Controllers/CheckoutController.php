<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use App\Services\PaymongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index(SupabaseService $supabase)
    {
        $deliveryZones = $supabase->fetchDeliveryZones();
        return view('checkout', compact('deliveryZones'));
    }

    public function success(Request $request, SupabaseService $supabase, PaymongoService $paymongo)
    {
        $orderNumber = $request->query('order');

        if ($orderNumber) {
            // 🔥 FALLBACK: Verify payment directly with PayMongo
            // Runs when user lands on success page — doesn't depend on webhook
            $this->verifyPaymentOnSuccess($orderNumber, $supabase, $paymongo);
        }

        return view('success');
    }

    private function verifyPaymentOnSuccess($orderNumber, SupabaseService $supabase, PaymongoService $paymongo)
    {
        try {
            $order = $supabase->findOrderByNumber($orderNumber);

            if (!$order) {
                Log::warning("Success: order {$orderNumber} not found.");
                return;
            }

            // Already paid? Skip.
            if ($order['payment_status'] === 'paid') {
                Log::info("Success: order {$orderNumber} already paid.");
                return;
            }

            if (empty($order['payment_intent_id'])) {
                Log::warning("Success: order {$orderNumber} has no payment_intent_id.");
                return;
            }

            // Query PayMongo directly
            $pi = $paymongo->getPaymentIntent($order['payment_intent_id']);

            if (!$pi) {
                Log::warning("Success: could not fetch PI for {$orderNumber}.");
                return;
            }

            $status = $pi['data']['attributes']['status'] ?? 'unknown';
            Log::info("Success: PI status for {$orderNumber} is {$status}");

            // PayMongo statuses: awaiting_payment_method, awaiting_next_action, processing, succeeded
            if ($status === 'succeeded') {
                // Update order
                $supabase->updateOrder($order['id'], [
                    'payment_status' => 'paid',
                    'status' => 'paid',
                    'updated_at' => now()->toIso8601String(),
                ]);

                // Decrement stock
                foreach ($order['items'] as $item) {
                    $product = $supabase->fetchProductById($item['product_id']);
                    if ($product) {
                        $newStock = max(0, $product['stock'] - $item['quantity']);
                        $supabase->updateProductStock($item['product_id'], $newStock);
                        Log::info("Stock updated for {$product['name']}: {$product['stock']} → {$newStock}");
                    }
                }

                Log::info("✅ Success: order {$orderNumber} marked as paid (fallback).");
            } else {
                Log::info("Success: order {$orderNumber} not yet paid. Status: {$status}");
            }

        } catch (\Exception $e) {
            Log::error("Success verification failed: " . $e->getMessage());
        }
    }
}