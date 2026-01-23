<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_name',
        'total_amount',
        'payment_type',
        'order_status',
        'created_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getFormattedAmountAttribute()
    {
        return '₹' . number_format($this->total_amount, 2);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-warning',
            'processing' => 'bg-info',
            'shipped' => 'bg-primary',
            'delivered' => 'bg-success',
            'cancelled' => 'bg-danger',
        ];

        $badgeClass = $badges[$this->order_status] ?? 'bg-secondary';
        return '<span class="badge ' . $badgeClass . '">' . ucfirst($this->order_status) . '</span>';
    }

    public function getPaymentTypeBadgeAttribute()
    {
        $badgeClass = $this->payment_type === 'COD' ? 'bg-success' : 'bg-info';
        return '<span class="badge ' . $badgeClass . '">' . $this->payment_type . '</span>';
    }
}
