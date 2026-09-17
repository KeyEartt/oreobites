<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymongoService
{
    protected $secretKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->secretKey = config('paymongo.secret_key');
        $this->apiUrl = config('paymongo.api_url');
    }

    private function client()
    {
        return Http::withBasicAuth($this->secretKey, '')
            ->baseUrl($this->apiUrl)
            ->withOptions(['verify' => false])
            ->acceptJson()
            ->contentType('application/json');
    }

    public function createPaymentIntent($amountInCentavos, $description)
    {
        $response = $this->client()->post('/payment_intents', [
            'data' => [
                'attributes' => [
                    'amount' => $amountInCentavos,
                    'currency' => 'PHP',
                    'payment_method_allowed' => ['gcash'],
                    'description' => $description,
                ],
            ],
        ]);

        if ($response->failed()) {
            Log::error('PayMongo createPaymentIntent failed: ' . $response->body());
            return null;
        }

        return $response->json();
    }

    public function createPaymentMethod()
    {
        $response = $this->client()->post('/payment_methods', [
            'data' => ['attributes' => ['type' => 'gcash']],
        ]);

        if ($response->failed()) {
            Log::error('PayMongo createPaymentMethod failed: ' . $response->body());
            return null;
        }

        return $response->json();
    }

    public function attachPaymentMethod($paymentIntentId, $paymentMethodId, $returnUrl)
    {
        $response = $this->client()->post("/payment_intents/{$paymentIntentId}/attach", [
            'data' => [
                'attributes' => [
                    'payment_method' => $paymentMethodId,
                    'return_url' => $returnUrl,
                ],
            ],
        ]);

        if ($response->failed()) {
            Log::error('PayMongo attachPaymentMethod failed: ' . $response->body());
            return null;
        }

        return $response->json();
    }

    /**
     * 🔥 NEW: Fetch current Payment Intent status from PayMongo
     */
    public function getPaymentIntent($paymentIntentId)
    {
        $response = $this->client()->get("/payment_intents/{$paymentIntentId}");

        if ($response->failed()) {
            Log::error('PayMongo getPaymentIntent failed: ' . $response->body());
            return null;
        }

        return $response->json();
    }

    public function verifyWebhookSignature($rawBody, $signatureHeader)
    {
        $secret = config('paymongo.webhook_secret');

        if (!$secret || !$signatureHeader) {
            return false;
        }

        $computed = hash_hmac('sha256', $rawBody, $secret);
        return hash_equals($computed, $signatureHeader);
    }
}