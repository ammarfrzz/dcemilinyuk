<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'order_date',
        'items_count',
        'product_name',
        'total_amount',
        'status',
    ];

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Selesai' => 'badge-success',
            'Diproses' => 'badge-warning',
            'Dikirim' => 'badge-info',
            'Dibatalkan' => 'badge-danger',
            default => 'badge-secondary',
        };
    }
}
