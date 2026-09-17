<?php

namespace App\Console\Commands;

use App\Services\SupabaseService;
use Illuminate\Console\Command;

class ExpireUnpaidOrders extends Command
{
    protected $signature = 'orders:expire';
    protected $description = 'Cancel unpaid orders older than 30 minutes';

    public function handle(SupabaseService $supabase)
    {
        $expired = $supabase->expireUnpaidOrders();
        $count = is_array($expired) ? count($expired) : 0;
        $this->info("Expired {$count} unpaid order(s).");
        return Command::SUCCESS;
    }
}