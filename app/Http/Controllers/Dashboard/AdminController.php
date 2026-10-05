<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private const TZ = 'Asia/Manila';

    public function index(SupabaseService $supabase)
    {
        $products = $supabase->fetchAllProducts();
        $orders   = $supabase->fetchAllOrders();

        $metrics = $this->computeMetrics($orders);

        return view('dashboard.admin', array_merge(
            [
                'products' => $products,
                'orders'   => $orders,
            ],
            $metrics
        ));
    }

    public function updateStock(Request $request, SupabaseService $supabase)
    {
        $validated = $request->validate([
            'product_id' => 'required|string',
            'stock'      => 'required|integer|min:0',
        ]);

        $updated = $supabase->updateProductStock(
            $validated['product_id'],
            $validated['stock']
        );

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Stock updated.' : 'Failed to update stock.',
        ]);
    }

    public function toggleProduct(Request $request, SupabaseService $supabase)
    {
        $validated = $request->validate([
            'product_id' => 'required|string',
            'is_active'  => 'required|boolean',
        ]);

        $updated = $supabase->updateProduct(
            $validated['product_id'],
            ['is_active' => $validated['is_active']]
        );

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Product updated.' : 'Failed to update product.',
        ]);
    }

    /**
     * Compute all dashboard KPIs from raw orders and products.
     */
    private function computeMetrics(array $orders): array
    {
        $now        = \Carbon\Carbon::now(self::TZ);
        $todayStart = $now->copy()->startOfDay();
        $start7d    = $now->copy()->subDays(7);
        $start30d   = $now->copy()->subDays(30);

        // ---------- Revenue (paid orders only) ----------
        $paidOrders    = array_filter($orders, fn ($o) => ($o['payment_status'] ?? '') === 'paid');
        $revenueToday  = 0;
        $revenue7d     = 0;
        $revenue30d    = 0;
        $revenueAll    = 0;

        foreach ($paidOrders as $o) {
            $total     = (int) ($o['total'] ?? 0);
            $createdAt = \Carbon\Carbon::parse($o['created_at'])->timezone(self::TZ);

            $revenueAll += $total;
            if ($createdAt->greaterThanOrEqualTo($todayStart)) $revenueToday += $total;
            if ($createdAt->greaterThanOrEqualTo($start7d))    $revenue7d    += $total;
            if ($createdAt->greaterThanOrEqualTo($start30d))   $revenue30d   += $total;
        }

        // ---------- Order funnel (all orders) ----------
        $funnel = [
            'pending'   => 0,
            'paid'      => 0,
            'preparing' => 0,
            'ready'     => 0,
            'picked_up' => 0,
            'cancelled' => 0,
        ];
        foreach ($orders as $o) {
            $s = $o['status'] ?? 'pending';
            if (isset($funnel[$s])) {
                $funnel[$s]++;
            }
        }

        // ---------- Last 30 days slice ----------
        $orders30d = array_filter($orders, function ($o) use ($start30d) {
            $createdAt = \Carbon\Carbon::parse($o['created_at'])->timezone(self::TZ);
            return $createdAt->greaterThanOrEqualTo($start30d);
        });

        // ---------- Top products (paid orders, last 30d) ----------
        $productAgg = [];
        foreach ($orders30d as $o) {
            if (($o['payment_status'] ?? '') !== 'paid') {
                continue;
            }
            foreach (($o['items'] ?? []) as $item) {
                $pid = $item['product_id'] ?? null;
                if (!$pid) {
                    continue;
                }

                if (!isset($productAgg[$pid])) {
                    $productAgg[$pid] = [
                        'name'    => $item['name'] ?? 'Item',
                        'qty'     => 0,
                        'revenue' => 0,
                    ];
                }

                $qty   = (int) ($item['quantity'] ?? 0);
                $price = (int) ($item['price'] ?? 0);

                $productAgg[$pid]['qty']     += $qty;
                $productAgg[$pid]['revenue'] += $qty * $price;
            }
        }
        usort($productAgg, fn ($a, $b) => $b['revenue'] <=> $a['revenue']);
        $topProducts = array_slice($productAgg, 0, 5);

        // ---------- Peak hours (last 30d) ----------
        $hourCounts = array_fill(0, 24, 0);
        foreach ($orders30d as $o) {
            $h = \Carbon\Carbon::parse($o['created_at'])->timezone(self::TZ)->hour;
            $hourCounts[$h]++;
        }

        // ---------- Cancellation rate (last 30d) ----------
        $cancelled30d = 0;
        foreach ($orders30d as $o) {
            if (($o['status'] ?? '') === 'cancelled') {
                $cancelled30d++;
            }
        }
        $orders30dCount    = count($orders30d);
        $cancellationRate  = $orders30dCount > 0
            ? round(($cancelled30d / $orders30dCount) * 100, 1)
            : 0.0;

        // ---------- Repeat customer rate (all time) ----------
        $emailCounts = [];
        foreach ($orders as $o) {
            $e = strtolower(trim($o['customer_email'] ?? ''));
            if ($e === '') {
                continue;
            }
            $emailCounts[$e] = ($emailCounts[$e] ?? 0) + 1;
        }
        $uniqueEmails = count($emailCounts);
        $repeatEmails = count(array_filter($emailCounts, fn ($c) => $c > 1));
        $repeatRate   = $uniqueEmails > 0
            ? round(($repeatEmails / $uniqueEmails) * 100, 1)
            : 0.0;

        return [
            'revenueToday'     => $revenueToday,
            'revenue7d'        => $revenue7d,
            'revenue30d'       => $revenue30d,
            'revenueAll'       => $revenueAll,
            'paidOrdersCount'  => count($paidOrders),
            'funnel'           => $funnel,
            'topProducts'      => $topProducts,
            'hourCounts'       => $hourCounts,
            'cancellationRate' => $cancellationRate,
            'cancelled30d'     => $cancelled30d,
            'orders30dCount'   => $orders30dCount,
            'repeatRate'       => $repeatRate,
            'uniqueEmails'     => $uniqueEmails,
            'repeatEmails'     => $repeatEmails,
            'totalOrders'      => count($orders),
        ];
    }
}