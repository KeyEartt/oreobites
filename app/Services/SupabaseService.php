<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected $client;

    public function __construct()
    {
        $url = config('supabase.url');
        $anonKey = config('supabase.anon_key');

        $this->client = new Client([
            'base_uri' => $url . '/rest/v1/',
            'headers' => [
                'apikey' => $anonKey,
                'Authorization' => 'Bearer ' . $anonKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    // ⬇️ THIS METHOD MUST EXIST ⬇️
    public function testConnection()
    {
        try {
            // Try to fetch just 1 product to test connection
            $response = $this->client->get('products?limit=1&select=id');
            return ['success' => true, 'message' => 'Connected to Supabase!'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchProducts()
    {
        try {
            $response = $this->client->get('products?is_active=eq.true&select=*');
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Supabase fetchProducts error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}