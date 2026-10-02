<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_income' => 'Rp 125.400.000',
            'income_change' => '+18%',
            'total_orders' => '1.284',
            'orders_change' => '+12.5%',
            'total_visits' => '42.890',
            'visits_change' => '+15%',
        ];

        $recentOrders = collect([
            (object) [
                'order_number' => 'ORD-20261001-001',
                'customer_name' => 'Eddria Wijaya',
                'order_date' => '01 Okt 2026, 14:15',
                'product_name' => 'Nugget Ayam Premium + Risol Mayo (x3)',
                'formatted_total' => 'Rp 98.000',
                'status' => 'Selesai',
                'status_badge_class' => 'badge-success',
            ],
            (object) [
                'order_number' => 'ORD-20261001-002',
                'customer_name' => 'Ammaliya Putri',
                'order_date' => '01 Okt 2026, 13:40',
                'product_name' => 'Kopi Susu Gula Aren + Cireng Salju',
                'formatted_total' => 'Rp 38.000',
                'status' => 'Diproses',
                'status_badge_class' => 'badge-warning',
            ],
            (object) [
                'order_number' => 'ORD-20261001-003',
                'customer_name' => 'Budi Santoso',
                'order_date' => '01 Okt 2026, 12:10',
                'product_name' => 'Siomay Frozen x2 + Dimsum Ayam',
                'formatted_total' => 'Rp 102.000',
                'status' => 'Dikirim',
                'status_badge_class' => 'badge-info',
            ],
            (object) [
                'order_number' => 'ORD-20260930-004',
                'customer_name' => 'Nabila Rahma',
                'order_date' => '30 Sep 2026, 19:22',
                'product_name' => 'Makaroni Pedas Daun Jeruk (x2)',
                'formatted_total' => 'Rp 30.000',
                'status' => 'Selesai',
                'status_badge_class' => 'badge-success',
            ],
            (object) [
                'order_number' => 'ORD-20260930-005',
                'customer_name' => 'Rizky Pratama',
                'order_date' => '30 Sep 2026, 17:05',
                'product_name' => 'Otak-Otak Ikan Tenggiri (x2)',
                'formatted_total' => 'Rp 44.000',
                'status' => 'Dibatalkan',
                'status_badge_class' => 'badge-danger',
            ],
        ]);

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}