<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function myOrder()
    {
        $orders = Order::with([
            'orderDetails.product.variants',
            'orderDetails.product.images',
        ])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();





        return view('client.orders.myorder', compact('orders'));
    }
    public function myOrderDetail($id)
    {

      $order = Order::with([
    'orderDetails.product.variants',
    'orderDetails.product.images',
])
    ->where('user_id', auth()->id())
    ->where('id', $id)
    ->firstOrFail();


        return view('client.orders.myorder-detail', compact('order'));
    }
}
