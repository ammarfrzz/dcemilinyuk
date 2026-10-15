<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $endOfLastMonth = $startOfMonth->copy()->subDay();

        $revenueQuery = fn ($from, $to) => Order::where('status', '!=', 'Dibatalkan')
            ->whereBetween('order_date', [$from, $to])
            ->sum('total_amount');

        $orderCountQuery = fn ($from, $to) => Order::whereBetween('order_date', [$from, $to])->count();

        $totalIncome = $revenueQuery($startOfMonth, $now);
        $lastMonthIncome = $revenueQuery($startOfLastMonth, $endOfLastMonth);

        $totalOrders = $orderCountQuery($startOfMonth, $now);
        $lastMonthOrders = $orderCountQuery($startOfLastMonth, $endOfLastMonth);

        // Pelanggan unik bulan ini vs bulan lalu (proxy untuk "kunjungan")
        $totalVisits = Order::whereNotNull('customer_email')
            ->whereBetween('order_date', [$startOfMonth, $now])
            ->distinct('customer_email')
            ->count('customer_email');
        $lastMonthVisits = Order::whereNotNull('customer_email')
            ->whereBetween('order_date', [$startOfLastMonth, $endOfLastMonth])
            ->distinct('customer_email')
            ->count('customer_email');

        $stats = [
            'total_income' => 'Rp ' . number_format($totalIncome, 0, ',', '.'),
            'income_change' => self::percentageChange($lastMonthIncome, $totalIncome),
            'total_orders' => number_format($totalOrders, 0, ',', '.'),
            'orders_change' => self::percentageChange($lastMonthOrders, $totalOrders),
            'total_visits' => number_format($totalVisits, 0, ',', '.'),
            'visits_change' => self::percentageChange($lastMonthVisits, $totalVisits),
        ];

        $recentOrders = Order::orderByDesc('order_date')->orderByDesc('id')->limit(5)->get();

        $summary = [
            'total_products' => Product::count(),
            'low_stock' => Product::whereColumn('stock', '<=', 'min_stock')->count(),
            'out_of_stock' => Product::where('stock', '<=', 0)->orWhere('available', false)->count(),
            'total_customers' => User::where('role', User::ROLE_USER)->count(),
            'pending_orders' => Order::whereIn('status', ['Menunggu Pembayaran', 'Diproses'])->count(),
        ];

        $topProducts = Product::orderByDesc('sold')->limit(5)->get(['id', 'name', 'sold', 'price', 'category']);

        $revenueChart = self::monthlyRevenueChart();

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'summary',
            'topProducts',
            'revenueChart'
        ));
    }

    /**
     * Persentase perubahan, guarded terhadap pembagi nol.
     */
    public static function percentageChange($old, $new): string
    {
        if ((int) $old === 0) {
            return (int) $new > 0 ? '+ baru' : '0%';
        }

        $change = ((int) $new - (int) $old) / (int) $old * 100;

        return ($change >= 0 ? '+' : '') . number_format($change, 1) . '%';
    }

    /**
     * Data pendapatan 6 bulan terakhir untuk grafik dashboard.
     *
     * @return array<int, array{label: string, total: int}>
     */
    protected static function monthlyRevenueChart(): array
    {
        $start = now()->copy()->subMonths(5)->startOfMonth();

        $rows = Order::where('status', '!=', 'Dibatalkan')
            ->where('order_date', '>=', $start)
            ->selectRaw("strftime('%Y-%m', order_date) AS period, SUM(total_amount) AS total")
            ->groupBy('period')
            ->pluck('total', 'period');

        $chart = [];

        for ($i = 0; $i < 6; $i++) {
            $month = $start->copy()->addMonths($i);
            $key = $month->format('Y-m');

            $chart[] = [
                'label' => $month->translatedFormat('M Y'),
                'total' => (int) ($rows[$key] ?? 0),
            ];
        }

        return $chart;
    }
}