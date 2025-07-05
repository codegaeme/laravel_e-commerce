<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ProductVariantValue extends Model
{
      protected $fillable = ['variant_id', 'attribute_value_id','value'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function attributeValue()
    {
        return $this->belongsTo(ProductAttributeValue::class, 'attribute_value_id');
    }
}
