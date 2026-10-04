<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'shipping_address',
        'order_notes',
        'product_id',
        'product_name',
        'unit_price',
        'items_count',
        'shipping_fee',
        'service_fee',
        'total_amount',
        'payment_method',
        'payment_status',
        'status',
        'order_date',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'integer',
            'items_count' => 'integer',
            'shipping_fee' => 'integer',
            'service_fee' => 'integer',
            'total_amount' => 'integer',
            'order_date' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }

    public function getFormattedShippingFeeAttribute(): string
    {
        return $this->shipping_fee > 0
            ? 'Rp ' . number_format($this->shipping_fee, 0, ',', '.')
            : 'Gratis';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'bca' => 'Transfer Bank BCA',
            'mandiri' => 'Transfer Bank Mandiri',
            'bri' => 'Transfer Bank BRI',
            'transfer_bank' => 'Transfer Bank',
            'qris' => 'QRIS (E-Wallet & M-Banking)',
            'cod' => 'Cash on Delivery (COD / Bayar di Tempat)',
            default => 'Belum Dipilih',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Selesai' => 'badge-success',
            'Diproses' => 'badge-warning',
            'Dikirim' => 'badge-info',
            'Dibatalkan' => 'badge-danger',
            'Menunggu Pembayaran' => 'badge-secondary',
            default => 'badge-secondary',
        };
    }
}
