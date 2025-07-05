<?php

namespace App\Models;

use App\Models\Admin\Product;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
   protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
