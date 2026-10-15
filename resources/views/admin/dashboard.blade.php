@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    {{-- Header --}}
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Kelola data dan operasional toko D'CemilinYuk Anda secara real-time.</p>
        </div>
    </div>

    {{-- Top 3 Metric Cards --}}
    <div class="metric-cards-grid">
        {{-- Card 1: Total Pendapatan --}}
        <div class="metric-card">
            <div class="metric-meta">
                <div class="metric-label">TOTAL PENDAPATAN</div>
                <div class="metric-value">{{ $stats['total_income'] }}</div>
                <div class="metric-change {{ str_starts_with($stats['income_change'], '-') ? 'negative' : 'positive' }}">
                    {{ $stats['income_change'] }} <span class="metric-change-desc">dari bulan lalu</span>
                </div>
            </div>
            <div class="metric-icon-circle">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
        </div>

        {{-- Card 2: Total Order --}}
        <div class="metric-card">
            <div class="metric-meta">
                <div class="metric-label">TOTAL ORDER</div>
                <div class="metric-value">{{ $stats['total_orders'] }}</div>
                <div class="metric-change {{ str_starts_with($stats['orders_change'], '-') ? 'negative' : 'positive' }}">
                    {{ $stats['orders_change'] }} <span class="metric-change-desc">dari bulan lalu</span>
                </div>
            </div>
            <div class="metric-icon-circle">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
        </div>

        {{-- Card 3: Pelanggan Unik --}}
        <div class="metric-card">
            <div class="metric-meta">
                <div class="metric-label">PELANGGAN UNIK</div>
                <div class="metric-value">{{ $stats['total_visits'] }}</div>
                <div class="metric-change {{ str_starts_with($stats['visits_change'], '-') ? 'negative' : 'positive' }}">
                    {{ $stats['visits_change'] }} <span class="metric-change-desc">dari bulan lalu</span>
                </div>
            </div>
            <div class="metric-icon-circle">
                <i class="fa-solid fa-eye"></i>
            </div>
        </div>
    </div>

    {{-- Ringkasan Operasional --}}
    <div class="metric-cards-grid">
        <div class="metric-card">
            <div class="metric-meta">
                <div class="metric-label">PESANAN DIPROSES</div>
                <div class="metric-value">{{ $summary['pending_orders'] }}</div>
            </div>
            <div class="metric-icon-circle"><i class="fa-solid fa-hourglass-half"></i></div>
        </div>
        <div class="metric-card">
            <div class="metric-meta">
                <div class="metric-label">STOK MENIPIS</div>
                <div class="metric-value">{{ $summary['low_stock'] }}</div>
            </div>
            <div class="metric-icon-circle"><i class="fa-solid fa-triangle-exclamation"></i></div>
        </div>
        <div class="metric-card">
            <div class="metric-meta">
                <div class="metric-label">STOK HABIS</div>
                <div class="metric-value">{{ $summary['out_of_stock'] }}</div>
            </div>
            <div class="metric-icon-circle"><i class="fa-solid fa-circle-xmark"></i></div>
        </div>
        <div class="metric-card">
            <div class="metric-meta">
                <div class="metric-label">TOTAL PELANGGAN</div>
                <div class="metric-value">{{ $summary['total_customers'] }}</div>
            </div>
            <div class="metric-icon-circle"><i class="fa-solid fa-users"></i></div>
        </div>
    </div>

    <div class="table-responsive">
        <div class="content-card">
            <div class="content-card-header">
                <h2 class="card-title">Produk Terlaris</h2>
                <a href="{{ route('admin.products.index') }}" class="card-link-orange">Kelola Produk</a>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>PRODUK</th>
                        <th>KATEGORI</th>
                        <th>TERJUAL</th>
                        <th>HARGA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topProducts as $product)
                        <tr>
                            <td class="text-bold">{{ $product->name }}</td>
                            <td>{{ $product->display_category }}</td>
                            <td>{{ number_format($product->sold, 0, ',', '.') }}</td>
                            <td class="price-text">{{ $product->formatted_price }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #94a3b8; padding: 30px;">
                                Belum ada produk untuk ditampilkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pesanan Terkini Card --}}
    <div class="content-card">
        <div class="content-card-header">
            <h2 class="card-title">Pesanan Terkini</h2>
            <a href="{{ route('admin.orders.index') }}" class="card-link-orange">Lihat Semua</a>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>NO. PESANAN</th>
                        <th>PELANGGAN</th>
                        <th>TANGGAL</th>
                        <th>PRODUK YANG DIBELI</th>
                        <th>JUMLAH</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td class="text-bold">{{ $order->order_number }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->order_date?->translatedFormat('d M Y, H:i') ?? '-' }}</td>
                            <td>{{ $order->product_name }}</td>
                            <td class="price-text">{{ $order->formatted_total }}</td>
                            <td>
                                <span class="badge {{ $order->status_badge_class }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 30px;">
                                Belum ada data pesanan terkini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
