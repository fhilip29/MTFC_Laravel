<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'description', 'price', 'image', 'stock', 'category', 'status',
        'is_promo', 'original_price', 'promo_ends_at'
    ];

    // Define the relationship with the OrderItem model
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class); // Assuming 'order_items' is the related table
    }

    // Calculate total number of purchases for this product
    public function totalPurchases()
    {
        return $this->orderItems->sum('quantity'); // Summing the quantity sold for this product
    }

    public function getImgUrlAttribute()
    {
        return asset('storage/products/' . $this->image);
    }

}


