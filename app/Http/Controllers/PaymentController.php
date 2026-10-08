<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use App\Services\PaymongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    public function create(
        Request $request,
        SupabaseService $supabase,
        PaymongoService $paymongo
    ) {
        $authUser = Session::get('auth_user');
        if (!$authUser) {
            return response()->json(['error' => 'Please log in to place an order.'], 401);
        }

        $validated = $request->validate([
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|string',
            'items.*.quantity'     => 'required|integer|min:1',
            'customer_name'        => 'required|string|max:255',
            'customer_phone'       => 'required|string|max:20',
            'customer_email'       => 'required|email',
            'delivery_type'        => 'required|in:pickup,standard,same_day',
            'delivery_address'     => 'nullable|string',
        ]);

        $zones = $supabase->fetchDeliveryZones();
        $zone  = collect($zones)->firstWhere('type', $validated['delivery_type']);
        if (!$zone) {
            return response()->json(['error' => 'Invalid delivery option.'], 400);
        }
        $deliveryFee = (int) $zone['fee'];
        $eta = $validated['delivery_type'] === 'pickup'
            ? 'Ready immediately'
            : $zone['estimated_minutes'] . ' minutes';

        $subtotal      = 0;
        $enrichedItems = [];

        foreach ($validated['items'] as $item) {
            $product = $supabase->fetchProductById($item['product_id']);
            if (!$product) {
                return response()->json(['error' => 'Product not found.'], 400);
            }
            if ($product['stock'] < $item['quantity']) {
                return response()->json([
                    'error' => "Only {$product['stock']} left of \"{$product['name']}\".",
                ], 400);
            }

            $lineTotal = $product['price'] * $item['quantity'];
            $subtotal += $lineTotal;

            $enrichedItems[] = [
                'product_id' => $product['id'],
                'name'       => $product['name'],
                'variant'    => $product['variant'] ?? null,
                'image_url'  => $product['image_url'] ?? null,
                'price'      => (int) $product['price'],
                'quantity'   => (int) $item['quantity'],
                'line_total' => $lineTotal,
            ];
        }

        $total            = $subtotal + $deliveryFee;
        $amountInCentavos = (int) round($total * 100);

        $piResponse = $paymongo->createPaymentIntent(
            $amountInCentavos,
            "Oreo Bites order for {$validated['customer_name']}"
        );
        if (!$piResponse || empty($piResponse['data']['id'])) {
            return response()->json(['error' => 'Payment gateway error (PI).'], 502);
        }
        $paymentIntentId = $piResponse['data']['id'];

        $pmResponse = $paymongo->createPaymentMethod();
        if (!$pmResponse || empty($pmResponse['data']['id'])) {
            return response()->json(['error' => 'Payment gateway error (PM).'], 502);
        }
        $paymentMethodId = $pmResponse['data']['id'];

        $attachResponse = $paymongo->attachPaymentMethod($paymentIntentId, $paymentMethodId);
        if (!$attachResponse) {
            return response()->json(['error' => 'Payment gateway error (Attach).'], 502);
        }

        $qrImageUrl = $attachResponse['data']['attributes']['next_action']['code']['image_url'] ?? null;
        if (!$qrImageUrl) {
            Log::error('PayMongo attach returned no QR image', ['attach' => $attachResponse]);
            return response()->json(['error' => 'QR code generation failed.'], 502);
        }

        $testUrl = $attachResponse['data']['attributes']['next_action']['code']['test_url'] ?? null;

        $orderNumber = 'ORE-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        $orderData = [
            'order_number'      => $orderNumber,
            'user_id'           => $authUser['id'],
            'customer_name'     => $validated['customer_name'],
            'customer_phone'    => $validated['customer_phone'],
            'customer_email'    => $validated['customer_email'],
            'delivery_type'     => $validated['delivery_type'],
            'delivery_fee'      => $deliveryFee,
            'delivery_address'  => $validated['delivery_type'] === 'pickup'
                ? null
                : ($validated['delivery_address'] ?? null),
            'subtotal'          => $subtotal,
            'total'             => $total,
            'items'             => $enrichedItems,
            'payment_intent_id' => $paymentIntentId,
            'payment_status'    => 'unpaid',
            'status'            => 'pending',
            'eta'               => $eta,
        ];

        $savedOrder = $supabase->createOrder($orderData);
        if (!$savedOrder) {
            return response()->json(['error' => 'Failed to save order.'], 500);
        }

        return response()->json([
            'qr_image'          => $qrImageUrl,
            'test_url'          => $testUrl,
            'orderNumber'       => $orderNumber,
            'payment_intent_id' => $paymentIntentId,
        ]);
    }
}