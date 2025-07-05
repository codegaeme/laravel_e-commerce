<?php

namespace App\Models;

use App\Models\Admin\Product;
use App\Models\Admin\ProductVariant;
use Illuminate\Database\Eloquent\Model;

class CartItems extends Model
{
    protected $table = 'cart_items';

    protected $fillable = [
         'cart_id',
        'product_id',
        'variant_id',
        'quantity',
        'price',
        'is_variant',
    ];

    // Quan hệ ngược với Cart
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // Quan hệ với Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
