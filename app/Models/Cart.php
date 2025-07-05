<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
   protected $table = 'carts';

    protected $fillable = [
        'user_id',
        'session_id',
    ];

    // Liên kết với user (nếu có)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Liên kết với các item trong giỏ hàng
    public function items()
    {
        return $this->hasMany(CartItems::class); // CartItem sẽ là bảng lưu chi tiết từng sản phẩm
    }
}
