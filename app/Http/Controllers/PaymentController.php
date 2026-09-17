<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use App\Services\PaymongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function create(
        Request $request,
        SupabaseService $supabase,
        PaymongoService $paymongo
    ) {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'required|email',
            'delivery_type' => 'required|in:pickup,standard,same_day',
            'delivery_address' => 'nullable|string',
        ]);

        $items = $validated['items'];

        // 1. Get the delivery fee from DB (never trust the client)
        $zones = $supabase->fetchDeliveryZones();
        $zone = collect($zones)->firstWhere('type', $validated['delivery_type']);

        if (!$zone) {
            return response()->json(['error' => 'Invalid delivery option.'], 400);
        }

        $deliveryFee = $zone['fee'];
        $eta = $validated['delivery_type'] === 'pickup'
            ? 'Ready immediately'
            : $zone['estimated_minutes'] . ' minutes';

        // 2. Validate stock and recalculate subtotal server-side
        $subtotal = 0;
        foreach ($items as $item) {
            $product = $supabase->fetchProductById($item['product_id']);

            if (!$product) {
                return response()->json(['error' => "Product not found."], 400);
            }

            if ($product['stock'] < $item['quantity']) {
                return response()->json([
                    'error' => "Only {$product['stock']} left of \"{$product['name']}\".",
                ], 400);
            }

            $subtotal += $product['price'] * $item['quantity'];
        }

        $total = $subtotal + $deliveryFee;
        $amountInCentavos = (int) round($total * 100);

        // 3. Create Payment Intent
        $piResult = $paymongo->createPaymentIntent(
            $amountInCentavos,
            "Oreo Bites order for {$validated['customer_name']}"
        );

        if (!$piResult || !isset($piResult['data']['id'])) {
            return response()->json(['error' => 'Payment gateway error (PI).'], 500);
        }

        $paymentIntentId = $piResult['data']['id'];

        // 4. Create Payment Method
        $pmResult = $paymongo->createPaymentMethod();

        if (!$pmResult || !isset($pmResult['data']['id'])) {
            return response()->json(['error' => 'Payment gateway error (PM).'], 500);
        }

        $paymentMethodId = $pmResult['data']['id'];

        // 5. Generate unique order number
        $orderNumber = 'ORE-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // 6. Save order to Supabase BEFORE redirecting
        $orderData = [
            'order_number' => $orderNumber,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'],
            'delivery_type' => $validated['delivery_type'],
            'delivery_fee' => $deliveryFee,
            'delivery_address' => $validated['delivery_type'] === 'pickup'
                ? null
                : $validated['delivery_address'],
            'subtotal' => $subtotal,
            'total' => $total,
            'items' => $items,
            'payment_intent_id' => $paymentIntentId,
            'payment_status' => 'unpaid',
            'status' => 'pending',
            'eta' => $eta,
        ];

        $savedOrder = $supabase->createOrder($orderData);

        if (!$savedOrder) {
            return response()->json([
                'error' => 'Failed to save order. Please try again.',
            ], 500);
        }

        // 7. Attach payment method with return_url containing order number
        $returnUrl = route('success') . '?order=' . $orderNumber;
        $attachResult = $paymongo->attachPaymentMethod(
            $paymentIntentId,
            $paymentMethodId,
            $returnUrl
        );

        $redirectUrl = $attachResult['data']['attributes']['next_action']['redirect']['url'] ?? null;

        if (!$redirectUrl) {
            return response()->json([
                'error' => 'Payment gateway did not return a checkout URL.',
            ], 500);
        }

        // 8. Return redirect URL to frontend
        return response()->json([
            'redirectUrl' => $redirectUrl,
            'orderNumber' => $orderNumber,
        ]);
    }
}