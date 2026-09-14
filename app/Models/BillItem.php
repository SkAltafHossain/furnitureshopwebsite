<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'discount',
        'total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->with('category');
    }

    public function getFormattedTotalAttribute()
    {
        return '₹' . number_format($this->total, 2);
    }

    public function getFormattedUnitPriceAttribute()
    {
        return '₹' . number_format($this->unit_price, 2);
    }

    public function getFormattedDiscountAttribute()
    {
        return number_format($this->discount, 2);
    }
}
