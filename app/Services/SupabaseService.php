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
        $url        = config('supabase.url');
        $anonKey    = config('supabase.anon_key');
        $serviceKey = config('supabase.service_role_key');

        $this->client = new Client([
            'base_uri' => $url . '/rest/v1/',
            'headers'  => [
                'apikey'        => $anonKey,
                'Authorization' => 'Bearer ' . $anonKey,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
            'verify' => false,
        ]);

        $this->serviceClient = new Client([
            'base_uri' => $url . '/rest/v1/',
            'headers'  => [
                'apikey'        => $serviceKey,
                'Authorization' => 'Bearer ' . $serviceKey,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
            'verify' => false,
        ]);
    }

    // ===== PUBLIC =====

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

    // ===== PRODUCTS =====

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

    public function createProduct(array $data)
    {
        try {
            $response = $this->serviceClient->post('products', [
                'json'    => $data,
                'headers' => ['Prefer' => 'return=representation'],
            ]);
            $body = json_decode($response->getBody(), true);
            return $body[0] ?? null;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Log::error('createProduct FAILED (4xx): ' . $e->getResponse()->getBody()->getContents());
            Log::error('Payload: ' . json_encode($data));
            return null;
        } catch (\Exception $e) {
            Log::error('createProduct FAILED: ' . $e->getMessage());
            return null;
        }
    }

    public function updateProduct($productId, $data)
    {
        try {
            $this->serviceClient->patch("products?id=eq.{$productId}", ['json' => $data]);
            return true;
        } catch (\Exception $e) {
            Log::error('updateProduct: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProductStock($productId, $newStock)
    {
        try {
            $this->serviceClient->patch("products?id=eq.{$productId}", [
                'json' => ['stock' => $newStock],
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('updateProductStock: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteProduct($productId)
    {
        try {
            $this->serviceClient->delete("products?id=eq.{$productId}");
            return true;
        } catch (\Exception $e) {
            Log::error('deleteProduct: ' . $e->getMessage());
            return false;
        }
    }

    // ===== ORDERS =====

    public function createOrder($data)
    {
        try {
            $response = $this->serviceClient->post('orders', [
                'json'    => $data,
                'headers' => ['Prefer' => 'return=representation'],
            ]);
            $body = json_decode($response->getBody(), true);
            Log::info('Order created OK', ['response' => $body]);
            return $body[0] ?? $body;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Log::error('createOrder FAILED (4xx): ' . $e->getResponse()->getBody()->getContents());
            Log::error('Payload sent: ' . json_encode($data));
            return null;
        } catch (\Exception $e) {
            Log::error('createOrder FAILED: ' . $e->getMessage());
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
            $encoded = urlencode($orderNumber);
            $response = $this->serviceClient->get("orders?order_number=eq.{$encoded}");
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

    public function fetchPendingInPersonOrders()
    {
        try {
            $response = $this->serviceClient->get(
                'orders?status=eq.pending&payment_method=eq.in_person&order=created_at.asc'
            );
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('fetchPendingInPersonOrders: ' . $e->getMessage());
            return [];
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

    /**
     * Two independent cutoffs:
     *  - online:    30 minutes  (payment gateway expected to complete)
     *  - in_person: 24 hours    (customer has time to walk in)
     */
    public function expireUnpaidOrders()
    {
        try {
            $now = now()->utc()->format('Y-m-d\TH:i:s\Z');

            // Online — older than 30 min
            $cutoffOnline = now()->utc()->subMinutes(30)->format('Y-m-d\TH:i:s\Z');
            $this->serviceClient->patch(
                "orders?payment_status=eq.unpaid&status=eq.pending&payment_method=eq.online&created_at=lt.{$cutoffOnline}",
                ['json' => [
                    'status'         => 'cancelled',
                    'payment_status' => 'failed',
                    'updated_at'     => $now,
                ]]
            );

            // In-person — older than 24 hours
            $cutoffInPerson = now()->utc()->subHours(24)->format('Y-m-d\TH:i:s\Z');
            $this->serviceClient->patch(
                "orders?payment_status=eq.unpaid&status=eq.pending&payment_method=eq.in_person&created_at=lt.{$cutoffInPerson}",
                ['json' => [
                    'status'         => 'cancelled',
                    'payment_status' => 'failed',
                    'updated_at'     => $now,
                ]]
            );

            return true;
        } catch (\Exception $e) {
            Log::error('expireUnpaidOrders: ' . $e->getMessage());
            return false;
        }
    }

    // ===== PROFILES =====

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

    public function createProfile($data)
    {
        try {
            $response = $this->serviceClient->post('profiles', [
                'json'    => $data,
                'headers' => ['Prefer' => 'return=representation'],
            ]);
            $body = json_decode($response->getBody(), true);
            return $body[0] ?? null;
        } catch (\Exception $e) {
            Log::error('createProfile: ' . $e->getMessage());
            return null;
        }
    }

    public function updateProfile($profileId, $data)
    {
        try {
            $this->serviceClient->patch("profiles?id=eq.{$profileId}", ['json' => $data]);
            return true;
        } catch (\Exception $e) {
            Log::error('updateProfile: ' . $e->getMessage());
            return false;
        }
    }
}