<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'sku', 'price', 'sale_price', 'quantity', 'image'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function values()
    {
        return $this->hasMany(ProductVariantValue::class, 'variant_id');
    }

    public function images()
    {
        return $this->hasMany(ProductVariantImage::class, 'variant_id');
    }
}
