<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'subtotal',
        'total_amount',
        'payment_method',
        'notes',
        'pdf_path',
        'is_sent',
        'sent_at',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedTotalAttribute()
    {
        return '₹' . number_format($this->total_amount, 2);
    }

    public function getFormattedSubtotalAttribute()
    {
        return '₹' . number_format($this->subtotal, 2);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bill) {
            if (empty($bill->bill_number)) {
                $bill->bill_number = 'BILL-' . str_pad(Bill::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
