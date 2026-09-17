<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected $client;
    protected $serviceClient;

    public function __construct()
    {
        $url = config('supabase.url');
        $anonKey = config('supabase.anon_key');
        $serviceKey = config('supabase.service_role_key');

        $this->client = new Client([
            'base_uri' => $url . '/rest/v1/',
            'headers' => [
                'apikey' => $anonKey,
                'Authorization' => 'Bearer ' . $anonKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false, // Windows SSL fix (remove in production)
        ]);

        $this->serviceClient = new Client([
            'base_uri' => $url . '/rest/v1/',
            'headers' => [
                'apikey' => $serviceKey,
                'Authorization' => 'Bearer ' . $serviceKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false,
        ]);
    }

    // ===== PUBLIC (anon key) =====

    public function fetchProducts()
    {
        try {
            $response = $this->client->get('products?is_active=eq.true&order=created_at.asc');
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('fetchProducts: ' . $e->getMessage());
            return [];
        }
    }

    public function fetchDeliveryZones()
    {
        try {
            $response = $this->client->get('delivery_zones?is_active=eq.true');
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('fetchDeliveryZones: ' . $e->getMessage());
            return [];
        }
    }

    // ===== SERVICE ROLE (bypass RLS) =====

    public function fetchProductById($id)
    {
        try {
            $response = $this->serviceClient->get("products?id=eq.{$id}&select=*");
            $data = json_decode($response->getBody(), true);
            return $data[0] ?? null;
        } catch (\Exception $e) {
            Log::error('fetchProductById: ' . $e->getMessage());
            return null;
        }
    }

    public function updateProductStock($productId, $newStock)
    {
        try {
            $this->serviceClient->patch("products?id=eq.{$productId}", [
                'json' => ['stock' => $newStock]
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('updateProductStock: ' . $e->getMessage());
            return false;
        }
    }

    public function createOrder($data)
{
    try {
        $response = $this->serviceClient->post('orders', [
            'json' => $data,
            'headers' => ['Prefer' => 'return=representation'],
        ]);
        $body = json_decode($response->getBody(), true);
        \Log::info('Order created OK', ['response' => $body]);
        return $body[0] ?? $body;

    } catch (\GuzzleHttp\Exception\ClientException $e) {
        $responseBody = $e->getResponse()->getBody()->getContents();
        \Log::error('createOrder FAILED (4xx): ' . $responseBody);
        \Log::error('Payload sent: ' . json_encode($data));
        return null;

    } catch (\Exception $e) {
        \Log::error('createOrder FAILED: ' . $e->getMessage());
        \Log::error('Payload sent: ' . json_encode($data));
        return null;
    }
}

    public function updateOrder($orderId, $data)
    {
        try {
            $this->serviceClient->patch("orders?id=eq.{$orderId}", ['json' => $data]);
            return true;
        } catch (\Exception $e) {
            Log::error('updateOrder: ' . $e->getMessage());
            return false;
        }
    }

    public function findOrderByPaymentIntent($paymentIntentId)
    {
        try {
            $response = $this->serviceClient->get("orders?payment_intent_id=eq.{$paymentIntentId}");
            $data = json_decode($response->getBody(), true);
            return $data[0] ?? null;
        } catch (\Exception $e) {
            Log::error('findOrderByPaymentIntent: ' . $e->getMessage());
            return null;
        }
    }

    public function findOrderByNumber($orderNumber)
    {
        try {
            $response = $this->serviceClient->get("orders?order_number=eq.{$orderNumber}");
            $data = json_decode($response->getBody(), true);
            return $data[0] ?? null;
        } catch (\Exception $e) {
            Log::error('findOrderByNumber: ' . $e->getMessage());
            return null;
        }
    }

    public function fetchAllOrders()
    {
        try {
            $response = $this->serviceClient->get('orders?order=created_at.desc');
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('fetchAllOrders: ' . $e->getMessage());
            return [];
        }
    }

        /**
     * Fetch ALL products (including inactive) for admin
     */
    public function fetchAllProducts()
    {
        try {
            $response = $this->serviceClient->get('products?order=created_at.asc');
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('fetchAllProducts: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch only orders that need action (paid, preparing, ready)
     */
    public function fetchActiveOrders()
    {
        try {
            $response = $this->serviceClient->get(
                'orders?status=in.(paid,preparing,ready)&order=created_at.asc'
            );
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('fetchActiveOrders: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update a product (partial update)
     */
    public function updateProduct($productId, $data)
    {
        try {
            $this->serviceClient->patch("products?id=eq.{$productId}", [
                'json' => $data,
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('updateProduct: ' . $e->getMessage());
            return false;
        }
    }

        // ===== AUTH METHODS =====

    public function findProfileByEmail($email)
    {
        try {
            $response = $this->serviceClient->get(
                "profiles?email=eq." . urlencode($email) . "&select=*"
            );
            $data = json_decode($response->getBody(), true);
            return $data[0] ?? null;
        } catch (\Exception $e) {
            Log::error('findProfileByEmail: ' . $e->getMessage());
            return null;
        }
    }

    public function findProfileById($id)
    {
        try {
            $response = $this->serviceClient->get("profiles?id=eq.{$id}&select=*");
            $data = json_decode($response->getBody(), true);
            return $data[0] ?? null;
        } catch (\Exception $e) {
            Log::error('findProfileById: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Expire old unpaid orders (30 min timeout)
     */
    public function expireUnpaidOrders()
    {
        try {
            $cutoff = now()->subMinutes(30)->toIso8601String();
            $response = $this->serviceClient->patch(
                "orders?payment_status=eq.unpaid&status=eq.pending&created_at=lt.{$cutoff}",
                ['json' => [
                    'status' => 'cancelled',
                    'payment_status' => 'failed',
                    'updated_at' => now()->toIso8601String(),
                ]]
            );
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('expireUnpaidOrders: ' . $e->getMessage());
            return [];
        }
    }

        public function createProfile($data)
    {
        try {
            $response = $this->serviceClient->post('profiles', [
                'json' => $data,
                'headers' => ['Prefer' => 'return=representation'],
            ]);
            $body = json_decode($response->getBody(), true);
            return $body[0] ?? null;
        } catch (\Exception $e) {
            Log::error('createProfile: ' . $e->getMessage());
            return null;
        }
    }

    public function findOrdersByEmail($email)
    {
        try {
            $response = $this->serviceClient->get(
                "orders?customer_email=eq." . urlencode($email) . "&order=created_at.desc"
            );
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('findOrdersByEmail: ' . $e->getMessage());
            return [];
        }
    }

}