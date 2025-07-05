<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
      protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'note',
        'total_amount',
        'status',
    ];

    // Relationships
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
