<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminOrderController extends Controller
{
    /**
     * Data dummy pesanan mandiri tanpa database
     */
    public static function getDummyOrders()
    {
        return collect([
            (object) [
                'id' => 1,
                'order_number' => 'ORD-20261001-001',
                'customer_name' => 'Eddria Wijaya',
                'customer_email' => 'eddria@gmail.com',
                'product_name' => 'Nugget Ayam Premium + Risol Mayo',
                'order_date' => '01 Okt 2026',
                'items_count' => 3,
                'formatted_total' => 'Rp 98.000',
                'status' => 'Selesai',
                'status_badge_class' => 'badge-success',
            ],
            (object) [
                'id' => 2,
                'order_number' => 'ORD-20261001-002',
                'customer_name' => 'Ammaliya Putri',
                'customer_email' => 'ammaliya@gmail.com',
                'product_name' => 'Kopi Susu Gula Aren + Cireng Salju',
                'order_date' => '01 Okt 2026',
                'items_count' => 2,
                'formatted_total' => 'Rp 38.000',
                'status' => 'Diproses',
                'status_badge_class' => 'badge-warning',
            ],
            (object) [
                'id' => 3,
                'order_number' => 'ORD-20261001-003',
                'customer_name' => 'Budi Santoso',
                'customer_email' => 'budi.santoso@yahoo.com',
                'product_name' => 'Siomay Frozen (x2) + Dimsum Ayam',
                'order_date' => '01 Okt 2026',
                'items_count' => 5,
                'formatted_total' => 'Rp 102.000',
                'status' => 'Dikirim',
                'status_badge_class' => 'badge-info',
            ],
            (object) [
                'id' => 4,
                'order_number' => 'ORD-20260930-004',
                'customer_name' => 'Nabila Rahma',
                'customer_email' => 'nabila.r@outlook.com',
                'product_name' => 'Makaroni Pedas Daun Jeruk (x2)',
                'order_date' => '30 Sep 2026',
                'items_count' => 2,
                'formatted_total' => 'Rp 30.000',
                'status' => 'Selesai',
                'status_badge_class' => 'badge-success',
            ],
            (object) [
                'id' => 5,
                'order_number' => 'ORD-20260930-005',
                'customer_name' => 'Rizky Pratama',
                'customer_email' => 'rizky.p@gmail.com',
                'product_name' => 'Otak-Otak Ikan Tenggiri (x2)',
                'order_date' => '30 Sep 2026',
                'items_count' => 4,
                'formatted_total' => 'Rp 44.000',
                'status' => 'Dibatalkan',
                'status_badge_class' => 'badge-danger',
            ],
            (object) [
                'id' => 6,
                'order_number' => 'ORD-20260929-006',
                'customer_name' => 'Dewi Lestari',
                'customer_email' => 'dewi.lestari@gmail.com',
                'product_name' => 'Es Teh Melati Jumbo (x2) + Cireng',
                'order_date' => '29 Sep 2026',
                'items_count' => 3,
                'formatted_total' => 'Rp 34.000',
                'status' => 'Selesai',
                'status_badge_class' => 'badge-success',
            ],
            (object) [
                'id' => 7,
                'order_number' => 'ORD-20260928-007',
                'customer_name' => 'Fajar Nugroho',
                'customer_email' => 'fajar.n@gmail.com',
                'product_name' => 'Basreng Pedas Nampol (x3)',
                'order_date' => '28 Sep 2026',
                'items_count' => 3,
                'formatted_total' => 'Rp 48.000',
                'status' => 'Selesai',
                'status_badge_class' => 'badge-success',
            ],
        ]);
    }

    public function index(Request $request)
    {
        $dbOrders = \App\Models\Order::latest()->get()->map(function ($order) {
            return (object) [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email ?: '-',
                'product_name' => $order->product_name . ' (' . $order->items_count . 'x)',
                'order_date' => $order->order_date ? $order->order_date->format('d M Y') : $order->created_at->format('d M Y'),
                'items_count' => $order->items_count,
                'formatted_total' => $order->formatted_total,
                'status' => $order->status,
                'status_badge_class' => $order->status_badge_class,
            ];
        });

        $orders = $dbOrders->concat(self::getDummyOrders());

        // Filter berdasarkan status
        if ($request->filled('status') && $request->status !== 'Semua') {
            $orders = $orders->filter(function ($order) use ($request) {
                return $order->status === $request->status;
            });
        }

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $orders = $orders->filter(function ($order) use ($search) {
                return str_contains(strtolower($order->order_number), $search)
                    || str_contains(strtolower($order->customer_name), $search)
                    || str_contains(strtolower($order->customer_email), $search)
                    || str_contains(strtolower($order->product_name), $search);
            });
        }

        $totalOrdersCount = $orders->count();
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $paginatedOrders = new LengthAwarePaginator(
            $orders->forPage($page, $perPage)->values(),
            $totalOrdersCount,
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('admin.orders.index', [
            'orders' => $paginatedOrders,
            'totalOrdersCount' => $totalOrdersCount,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = \App\Models\Order::find($id);
        if ($order) {
            $order->status = $request->status;
            $order->save();
            return back()->with('success', "Status pesanan {$order->order_number} berhasil diubah menjadi '{$request->status}'.");
        }

        return back()->with(
            'success',
            "Status pesanan berhasil diubah menjadi '{$request->status}' (Mode Dummy)."
        );
    }
}