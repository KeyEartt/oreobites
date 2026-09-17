<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use App\Services\PaymongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(
        Request $request,
        SupabaseService $supabase,
        PaymongoService $paymongo
    ) {
        // 🔥 CRITICAL: Use raw body for signature verification (not parsed)[reference:3]
        $rawBody = $request->getContent();
        $signature = $request->header('Paymongo-Signature');

        // 1. Verify signature
        if (!$paymongo->verifyWebhookSignature($rawBody, $signature)) {
            Log::warning('PayMongo webhook signature verification failed.');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $payload = json_decode($rawBody, true);
        $eventType = $payload['data']['attributes']['type'] ?? '';

        // 2. Only handle payment.paid events
        if ($eventType !== 'payment.paid') {
            Log::info("Ignoring webhook event: {$eventType}");
            return response()->json(['received' => true]);
        }

        // 3. Extract payment_intent_id (one level shallower than the payment resource)
        $paymentIntentId = $payload['data']['attributes']['data']['attributes']['payment_intent_id'] ?? null;

        if (!$paymentIntentId) {
            Log::error('Webhook missing payment_intent_id.');
            return response()->json(['error' => 'Missing payment_intent_id'], 400);
        }

        Log::info("Processing payment for intent: {$paymentIntentId}");

        // 4. Find the order
        $order = $supabase->findOrderByPaymentIntent($paymentIntentId);

        if (!$order) {
            Log::error("Order not found for payment_intent: {$paymentIntentId}");
            return response()->json(['error' => 'Order not found'], 404);
        }

        // 5. Idempotency: skip if already paid
        if ($order['payment_status'] === 'paid') {
            Log::info("Order {$order['id']} already paid. Skipping.");
            return response()->json(['received' => true]);
        }

        // 6. Mark order as paid
        $updated = $supabase->updateOrder($order['id'], [
            'payment_status' => 'paid',
            'status' => 'paid',
            'updated_at' => now()->toIso8601String(),
        ]);

        if (!$updated) {
            Log::error("Failed to update order {$order['id']}");
            return response()->json(['error' => 'Failed to update order'], 500);
        }

        // 7. Decrement stock for each item
        foreach ($order['items'] as $item) {
            $product = $supabase->fetchProductById($item['product_id']);

            if ($product) {
                $newStock = max(0, $product['stock'] - $item['quantity']);
                $supabase->updateProductStock($item['product_id'], $newStock);
                Log::info("Stock updated for {$product['name']}: {$product['stock']} → {$newStock}");
            }
        }

        Log::info("✅ Order {$order['order_number']} marked as paid and stock updated.");

        return response()->json(['received' => true]);
    }
}