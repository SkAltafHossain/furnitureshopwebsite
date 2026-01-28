<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'address_id',
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
            'delivered_confirmed' => 'bg-success',
            'cancelled' => 'bg-danger',
        ];

        $badgeClass = $badges[$this->order_status] ?? 'bg-secondary';
        $statusText = $this->order_status === 'delivered_confirmed' ? 'Delivered' : ucfirst($this->order_status);
        return '<span class="badge ' . $badgeClass . '">' . $statusText . '</span>';
    }

    public function getPaymentTypeBadgeAttribute()
    {
        $badgeClass = $this->payment_method === 'COD' ? 'bg-success' : 'bg-info';
        return '<span class="badge ' . $badgeClass . '">' . $this->payment_method . '</span>';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
    
    public function canBeConfirmedByUser()
    {
        return $this->order_status === 'delivered' && 
               $this->updated_at->diffInHours(now()) < 24;
    }
    
    public function confirmDelivery()
    {
        if ($this->canBeConfirmedByUser()) {
            $this->order_status = 'delivered_confirmed';
            $this->save();
            return true;
        }
        return false;
    }
}
