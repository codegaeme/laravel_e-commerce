<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class OrderManagerController extends Controller
{
    public function index(){

        $orders = Order::with([
            'orderDetails.product.variants',
            'orderDetails.product.images',
        ])

            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.orders.list',compact('orders'));
    }
  public function changeStt(Request $request)
{
    $order = Order::findOrFail($request->id);
    $current = $order->status;

    $statusFlow = [
        'pending' => ['confirmed', 'Đã xác nhận đơn hàng.'],
        'confirmed' => ['preparing', 'Đã xác nhận chuẩn bị đơn hàng.'],
        'preparing' => ['shipped', 'Đã chuyển qua đơn vị giao hàng.'],
        'shipped' => ['in_transit', 'Xác nhận đang giao hàng.'],
        'in_transit' => ['delivered', 'Xác nhận đã giao hàng thành công.'],
    ];

    if (array_key_exists($current, $statusFlow)) {
        [$nextStatus, $message] = $statusFlow[$current];
        $order->status = $nextStatus;
        $order->save();

        return redirect()->back()->with('success', $message);
    }

    return redirect()->back()->with('error', 'Trạng thái không hợp lệ.');
}

  public function changeSttCus (Request $request)
{
    $order = Order::findOrFail($request->id);
    $current = $order->status;

    $statusFlow = [
        'pending' => ['canceled', 'Hủy đơn hàng thànhn công.'],
        'delivered' => ['success', 'Xác nhận đã nhận được hàng'],
    ];

    if (array_key_exists($current, $statusFlow)) {
        [$nextStatus, $message] = $statusFlow[$current];
        $order->status = $nextStatus;
        $order->save();

        return redirect()->back()->with('success', $message);
    }

    return redirect()->back()->with('error', 'Trạng thái không hợp lệ.');
}

}
