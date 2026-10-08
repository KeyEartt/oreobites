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
            ['products' => $products, 'orders' => $orders],
            $metrics
        ));
    }

    public function storeProduct(Request $request, SupabaseService $supabase)
    {
        $validated = $this->validateProduct($request);

        $created = $supabase->createProduct([
            'name'        => $validated['name'],
            'variant'     => $validated['variant'],
            'description' => $validated['description'] ?? '',
            'price'       => (int) $validated['price'],
            'stock'       => (int) $validated['stock'],
            'image_url'   => $validated['image_url'] ?? null,
            'is_active'   => (bool) $validated['is_active'],
        ]);

        return response()->json([
            'success' => (bool) $created,
            'message' => $created ? 'Product created.' : 'Failed to create product.',
            'product' => $created,
        ]);
    }

    public function updateProduct(Request $request, SupabaseService $supabase)
    {
        $validated = $this->validateProduct($request);
        $id = $request->input('id');

        if (!$id) return response()->json(['success' => false, 'message' => 'Missing product ID.'], 400);

        $updated = $supabase->updateProduct($id, [
            'name'        => $validated['name'],
            'variant'     => $validated['variant'],
            'description' => $validated['description'] ?? '',
            'price'       => (int) $validated['price'],
            'stock'       => (int) $validated['stock'],
            'image_url'   => $validated['image_url'] ?? null,
            'is_active'   => (bool) $validated['is_active'],
        ]);

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Product updated.' : 'Failed to update product.',
        ]);
    }

    public function deleteProduct(Request $request, SupabaseService $supabase)
    {
        $id = $request->input('id');
        if (!$id) return response()->json(['success' => false, 'message' => 'Missing product ID.'], 400);

        $deleted = $supabase->deleteProduct($id);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Product deleted.' : 'Failed to delete product.',
        ]);
    }

    public function updateStock(Request $request, SupabaseService $supabase)
    {
        $validated = $request->validate([
            'product_id' => 'required|string',
            'stock'      => 'required|integer|min:0',
        ]);

        $updated = $supabase->updateProductStock($validated['product_id'], $validated['stock']);

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

    public function markPaid(Request $request, SupabaseService $supabase)
    {
        $validated = $request->validate([
            'order_id' => 'required|string',
        ]);

        $updated = $supabase->updateOrder($validated['order_id'], [
            'payment_status' => 'paid',
            'status'         => 'paid',
            'updated_at'     => now()->utc()->format('Y-m-d\TH:i:s\Z'),
        ]);

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Payment confirmed.' : 'Failed to confirm payment.',
        ]);
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'name'        => 'required|string|max:255',
            'variant'     => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'price'       => 'required|integer|min:0|max:100000',
            'stock'       => 'required|integer|min:0|max:100000',
            'image_url'   => 'nullable|string|max:500',
            'is_active'   => 'required|boolean',
        ]);
    }

    private function computeMetrics(array $orders): array
    {
        $now        = \Carbon\Carbon::now(self::TZ);
        $todayStart = $now->copy()->startOfDay();
        $start7d    = $now->copy()->subDays(7);
        $start30d   = $now->copy()->subDays(30);

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

        $funnel = ['pending'=>0,'paid'=>0,'preparing'=>0,'ready'=>0,'picked_up'=>0,'cancelled'=>0];
        foreach ($orders as $o) {
            $s = $o['status'] ?? 'pending';
            if (isset($funnel[$s])) $funnel[$s]++;
        }

        $orders30d = array_filter($orders, function ($o) use ($start30d) {
            $createdAt = \Carbon\Carbon::parse($o['created_at'])->timezone(self::TZ);
            return $createdAt->greaterThanOrEqualTo($start30d);
        });

        $productAgg = [];
        foreach ($orders30d as $o) {
            if (($o['payment_status'] ?? '') !== 'paid') continue;
            foreach (($o['items'] ?? []) as $item) {
                $pid = $item['product_id'] ?? null;
                if (!$pid) continue;
                if (!isset($productAgg[$pid])) {
                    $productAgg[$pid] = ['name'=>$item['name'] ?? 'Item','qty'=>0,'revenue'=>0];
                }
                $qty   = (int) ($item['quantity'] ?? 0);
                $price = (int) ($item['price'] ?? 0);
                $productAgg[$pid]['qty']     += $qty;
                $productAgg[$pid]['revenue'] += $qty * $price;
            }
        }
        usort($productAgg, fn ($a, $b) => $b['revenue'] <=> $a['revenue']);
        $topProducts = array_slice($productAgg, 0, 5);

        $hourCounts = array_fill(0, 24, 0);
        foreach ($orders30d as $o) {
            $h = \Carbon\Carbon::parse($o['created_at'])->timezone(self::TZ)->hour;
            $hourCounts[$h]++;
        }

        $cancelled30d = 0;
        foreach ($orders30d as $o) if (($o['status'] ?? '') === 'cancelled') $cancelled30d++;

        $orders30dCount   = count($orders30d);
        $cancellationRate = $orders30dCount > 0 ? round(($cancelled30d / $orders30dCount) * 100, 1) : 0.0;

        $emailCounts = [];
        foreach ($orders as $o) {
            $e = strtolower(trim($o['customer_email'] ?? ''));
            if ($e === '') continue;
            $emailCounts[$e] = ($emailCounts[$e] ?? 0) + 1;
        }
        $uniqueEmails = count($emailCounts);
        $repeatEmails = count(array_filter($emailCounts, fn ($c) => $c > 1));
        $repeatRate   = $uniqueEmails > 0 ? round(($repeatEmails / $uniqueEmails) * 100, 1) : 0.0;

        return [
            'revenueToday' => $revenueToday, 'revenue7d' => $revenue7d,
            'revenue30d' => $revenue30d, 'revenueAll' => $revenueAll,
            'paidOrdersCount' => count($paidOrders), 'funnel' => $funnel,
            'topProducts' => $topProducts, 'hourCounts' => $hourCounts,
            'cancellationRate' => $cancellationRate, 'cancelled30d' => $cancelled30d,
            'orders30dCount' => $orders30dCount, 'repeatRate' => $repeatRate,
            'uniqueEmails' => $uniqueEmails, 'repeatEmails' => $repeatEmails,
            'totalOrders' => count($orders),
        ];
    }
}