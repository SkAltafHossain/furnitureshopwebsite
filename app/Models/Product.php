<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock',
        'image',
        'status',
        'featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'stock' => 'integer',
        'featured' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function allReviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }

    public function getAverageRatingAttribute()
    {
        return number_format($this->averageRating(), 1);
    }

    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    public function canBeReviewedBy($userId)
    {
        if (!$userId) return false;
        
        return OrderItem::whereHas('order', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('order_status', 'delivered_confirmed');
        })->where('product_id', $this->id)
          ->whereDoesntHave('product.reviews', function ($query) use ($userId) {
              $query->where('user_id', $userId);
          })->exists();
    }

    public function getFormattedPriceAttribute()
    {
        return '₹' . number_format($this->price, 2);
    }

    public function getFormattedDiscountPriceAttribute()
    {
        return $this->discount_price ? '₹' . number_format($this->discount_price, 2) : null;
    }

    public function getIsInStockAttribute()
    {
        return $this->stock > 0;
    }
}
