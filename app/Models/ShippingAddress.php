<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
  protected $fillable = ['user_id', 'name', 'phone', 'province', 'district', 'ward', 'address_detail','address'];

  public function user()
{
    return $this->belongsTo(User::class);
}


}
