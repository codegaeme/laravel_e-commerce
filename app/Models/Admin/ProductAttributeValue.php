<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    protected $fillable = ['attribute_id', 'value'];

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    public function variantValues()
    {
        return $this->hasMany(ProductVariantValue::class, 'attribute_value_id');
    }
}
