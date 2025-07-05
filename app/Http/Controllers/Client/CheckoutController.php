<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CartItems;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        if (isset($request->address_id) ){
            $id_address =$request->address_id;
            $shippingAddress = ShippingAddress::findOrFail($id_address);
        }else{
             $shippingAddress= '' ;
        }


        $subtotal = $request->total;
        $id_user = auth()->id();

        $shippingAddresses = ShippingAddress::where('user_id', $id_user)->get();

        $cartItems = CartItems::with('cart', 'product', 'variant')
            ->whereIn('id', $request->cartItem_id)
            ->get();

        return view('cart.checkout', compact('cartItems', 'subtotal','shippingAddresses','shippingAddress'));
    }
    public function spad()
    {
        return view('cart.shippingaddress');
    }
    public function spadPost(Request $re)
    {
        $re->validate([
            'name' => 'required|string|min:2|max:255',
            'phone' => 'required|digits_between:9,11',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'address_detail' => 'required|string|min:5|max:255',
            'address' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.min' => 'Tên phải có ít nhất 2 ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',

            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.digits_between' => 'Số điện thoại phải từ 9 đến 11 chữ số.',

            'province.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'district.required' => 'Vui lòng chọn quận/huyện.',
            'ward.required' => 'Vui lòng chọn phường/xã.',

            'address_detail.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'address_detail.min' => 'Địa chỉ chi tiết phải có ít nhất 5 ký tự.',
            'address_detail.max' => 'Địa chỉ chi tiết không được vượt quá 255 ký tự.',
        ]);

        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        $exists = ShippingAddress::where([
            ['user_id', auth()->id()],
            ['name', trim($re->name)],
            ['phone', trim($re->phone)],
            ['province', trim($re->province)],
            ['district', trim($re->district)],
            ['ward', trim($re->ward)],
            ['address_detail', trim($re->address_detail)],
        ])->exists();

        if ($exists) {
            return redirect()->back()->withInput()->withErrors([
                'address_detail' => 'Địa chỉ này đã tồn tại trong danh sách của bạn.',
            ]);
        }

        ShippingAddress::create([
            'user_id' => auth()->id(),
            'name' => trim($re->name),
            'phone' => trim($re->phone),
            'province' => trim($re->province),
            'district' => trim($re->district),
            'ward' => trim($re->ward),
            'address_detail' => trim($re->address_detail),
            'address' => trim($re->full_address),
        ]);

        return redirect()->route('shippingAdddress')->with('success', 'Địa chỉ đã được thêm thành công!');
    }


    public function checkoutPost(Request $request)
    {

            if ($request->isMethod('post')) {
                       if ($request -> payment_method === 'cod') {

            $order = Order::create([
                'user_id' => auth()->user()->id,
                'customer_name' => $request -> fullname,
                'customer_phone' => $request -> phone,
                'customer_address' => $request -> address,
                'note' => $request -> note,
                'total_amount' => $request -> total,
                'payment_method' => $request ->payment_method
            ]);

            $order_id =  $order->id;

         $cartItems = CartItems::with('cart', 'product', 'variant')
            ->whereIn('id', $request->cartItem_id)
            ->get();
                foreach($cartItems as $item){
                    $subtotal = $item-> price * $item -> quantity ;

                    if ($item->is_variant == 0 ) {
                       $variant_id = null;
                    }elseif ($item->is_variant == 1 ) {
                       $variant_id = $item ->variant_id;
                    }

                    $data = [
                        'order_id' => $order_id,
                        'variant_id' => $variant_id,
                        'product_id' => $item->product_id,
                        'quantity' => $item -> quantity,
                        'price' => $item -> price,
                        'subtotal' => $subtotal
                    ];
                    OrderDetail::create($data);


                }
                        return redirect('/success')->with('success','Bạn đã đặt hàng thành công');

        }elseif ($request -> payment_method === 'vnpay') {
            dd($request);
        }
            }


    }
public function success(){
    return view('cart.success');
}
}
