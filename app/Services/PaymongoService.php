<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymongoService
{
    protected string $secretKey;
    protected string $apiUrl;

    public function __construct()
    {
        $this->secretKey = config('paymongo.secret_key');
        $this->apiUrl    = rtrim(config('paymongo.api_url', 'https://api.paymongo.com/v1'), '/');
    }

    private function client()
    {
        return Http::withBasicAuth($this->secretKey, '')
            ->baseUrl($this->apiUrl)
            ->withOptions(['verify' => false])
            ->acceptJson()
            ->contentType('application/json')
            ->timeout(30);
    }

    public function createPaymentIntent(int $amountCentavos, string $description): ?array
    {
        $response = $this->client()->post('/payment_intents', [
            'data' => ['attributes' => [
                'amount'                 => $amountCentavos,
                'currency'               => 'PHP',
                'payment_method_allowed' => ['qrph'],
                'description'            => $description,
                'capture_type'           => 'automatic',
            ]],
        ]);

        if ($response->failed()) {
            Log::error('PayMongo createPaymentIntent failed', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            return null;
        }
        return $response->json();
    }

    public function createPaymentMethod(): ?array
    {
        $response = $this->client()->post('/payment_methods', [
            'data' => ['attributes' => ['type' => 'qrph']],
        ]);

        if ($response->failed()) {
            Log::error('PayMongo createPaymentMethod failed', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            return null;
        }
        return $response->json();
    }

    public function attachPaymentMethod(string $paymentIntentId, string $paymentMethodId): ?array
    {
        $response = $this->client()->post("/payment_intents/{$paymentIntentId}/attach", [
            'data' => ['attributes' => [
                'payment_method' => $paymentMethodId,
            ]],
        ]);

        if ($response->failed()) {
            Log::error('PayMongo attachPaymentMethod failed', [
                'pi'     => $paymentIntentId,
                'pm'     => $paymentMethodId,
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            return null;
        }
        return $response->json();
    }

    public function getPaymentIntent(string $paymentIntentId): ?array
    {
        $response = $this->client()->get("/payment_intents/{$paymentIntentId}");
        if ($response->failed()) {
            Log::error('PayMongo getPaymentIntent failed', [
                'pi'     => $paymentIntentId,
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            return null;
        }
        return $response->json();
    }

    /**
     * PayMongo header format: t=<ts>,te=<hmac>,li=<hmac>
     * Signed payload: "<timestamp>.<rawBody>"
     * Rejects signatures older than 5 minutes (replay protection).
     */
    public function verifyWebhookSignature(string $rawBody, ?string $signatureHeader): bool
    {
        $secret = config('paymongo.webhook_secret');
        if (!$secret || !$signatureHeader) {
            return false;
        }

        $parts = [];
        foreach (explode(',', $signatureHeader) as $pair) {
            $kv = explode('=', $pair, 2);
            if (count($kv) === 2) {
                $parts[trim($kv[0])] = trim($kv[1]);
            }
        }

        $timestamp  = $parts['t']  ?? null;
        $candidates = array_filter([$parts['te'] ?? null, $parts['li'] ?? null]);

        if (!$timestamp || empty($candidates)) {
            return false;
        }

        // Replay protection — reject if timestamp is more than 5 minutes old
        if (abs(time() - (int) $timestamp) > 300) {
            Log::warning('PayMongo webhook timestamp out of tolerance', ['t' => $timestamp]);
            return false;
        }

        $computed = hash_hmac('sha256', $timestamp . '.' . $rawBody, $secret);

        foreach ($candidates as $sig) {
            if (hash_equals($computed, $sig)) {
                return true;
            }
        }
        return false;
    }
}