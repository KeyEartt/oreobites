<?php

namespace App\Console\Commands;

use App\Services\SupabaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestWebhook extends Command
{
    protected $signature = 'paymongo:test-webhook {order_number}';
    protected $description = 'Send a locally-signed fake payment.paid webhook to /api/webhook to test the handler.';

    public function handle(SupabaseService $supabase)
    {
        $orderNumber = strtoupper(trim($this->argument('order_number')));

        $order = $supabase->findOrderByNumber($orderNumber);
        if (!$order) {
            $this->error("Order {$orderNumber} not found.");
            return Command::FAILURE;
        }

        if (empty($order['payment_intent_id'])) {
            $this->error("Order {$orderNumber} has no payment_intent_id. Was it created before the PI fix?");
            return Command::FAILURE;
        }

        if ($order['payment_status'] === 'paid') {
            $this->warn("Order {$orderNumber} is already paid. Nothing to do.");
            return Command::SUCCESS;
        }

        $piId = $order['payment_intent_id'];

        // Build a fake event payload matching PayMongo's real shape
        $payload = json_encode([
            'data' => [
                'attributes' => [
                    'type' => 'payment.paid',
                    'data' => [
                        'attributes' => [
                            'payment_intent_id' => $piId,
                        ],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES);

        $secret    = config('paymongo.webhook_secret');
        $timestamp = (string) time();
        $hmac      = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
        $signature = "t={$timestamp},te={$hmac},li={$hmac}";

        $this->info("PI: {$piId}");
        $this->info("Signature: {$signature}");
        $this->newLine();

        $response = Http::withHeaders([
            'Paymongo-Signature' => $signature,
            'Content-Type'       => 'application/json',
        ])->withBody($payload, 'application/json')
          ->post(url('/api/webhook'));

        $this->info("HTTP {$response->status()}");
        $this->line($response->body());

        // Re-read the order to confirm the state changed
        $this->newLine();
        $updated = $supabase->findOrderByNumber($orderNumber);
        $this->info("After:");
        $this->line("  payment_status: " . ($updated['payment_status'] ?? '?'));
        $this->line("  status:         " . ($updated['status'] ?? '?'));

        return Command::SUCCESS;
    }
}