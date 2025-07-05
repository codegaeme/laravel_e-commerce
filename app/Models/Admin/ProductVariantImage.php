<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ProductVariantImage extends Model
{
     protected $fillable = [
        'variant_id', 'image', 'is_main',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
