<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    public const STATUS_AWAITING_PAYMENT = 'Menunggu Pembayaran';
    public const STATUS_PROCESSING = 'Diproses';
    public const STATUS_SHIPPED = 'Dikirim';
    public const STATUS_COMPLETED = 'Selesai';
    public const STATUS_CANCELLED = 'Dibatalkan';

    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PENDING_VERIFICATION = 'pending_verification';
    public const PAYMENT_STATUS_COD_PENDING = 'cod_pending';
    public const PAYMENT_STATUS_PAID = 'paid';

    public const STATUSES = [
        self::STATUS_AWAITING_PAYMENT,
        self::STATUS_PROCESSING,
        self::STATUS_SHIPPED,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_email', 'email');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', self::STATUS_CANCELLED);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeAwaitingPayment($query)
    {
        return $query->where('payment_status', self::PAYMENT_STATUS_PENDING);
    }

    public function getSubtotalAttribute(): int
    {
        return $this->unit_price * $this->items_count;
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
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
            self::STATUS_COMPLETED => 'badge-success',
            self::STATUS_PROCESSING => 'badge-warning',
            self::STATUS_SHIPPED => 'badge-info',
            self::STATUS_CANCELLED => 'badge-danger',
            self::STATUS_AWAITING_PAYMENT => 'badge-secondary',
            default => 'badge-secondary',
        };
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function getFormattedServiceFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->service_fee, 0, ',', '.');
    }
}