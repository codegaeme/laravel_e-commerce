<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Admin\Product;
use App\Models\Admin\ProductVariant;
use App\Models\Cart;
use App\Models\CartItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function add(Request $request)
    {


        // 1. Validate đầu vào
        $validated = $request->validate([
            'product_id'  => 'required|integer|gt:0',
            'is_variant'  => 'required|in:0,1',
            'num-product' => 'required|integer|min:1',
            'quantity'    => 'required|integer|min:0',
        ], [
            'required' => 'Vui lòng điền đầy đủ thông tin.',
            'integer'  => 'Trường :attribute phải là số.',
            'in'       => 'Trường :attribute không hợp lệ.',
            'min'      => 'Số lượng phải lớn hơn 0.',
        ]);

        $productId = $validated['product_id'];
        $isVariant = $validated['is_variant'];
        $quantity  = $validated['num-product'];
        $stock     = $validated['quantity'];

        // 2. Kiểm tra số lượng tồn kho
        if ($quantity > $stock) {
            return redirect()->back()->with('error', 'Số lượng trong kho không đủ.');
        }

        // 3. Lấy thông tin sản phẩm
        if ($isVariant == 0) {
            $product = Product::with(['category', 'images', 'variants.values.attributeValue.attribute'])
                ->findOrFail($productId);
            $price = (!is_null($product->price_sale) && $product->price_sale != 0)
                ? $product->price_sale
                : $product->price;


            $variant_id = null;
        } else {
            $variant = ProductVariant::with('product')->findOrFail($productId);
            $product = $variant->product;
            $price = $variant->price_sale ?? $variant->price;
            $variant_id = $variant->id;
        }

        // 4. Lấy hoặc tạo giỏ hàng
        $cart = Auth::check()
            ? Cart::firstOrCreate(['user_id' => Auth::id()])
            : Cart::firstOrCreate(['session_id' => $request->session()->getId()]);

        // 5. Kiểm tra sản phẩm đã có trong giỏ chưa


        if ($isVariant == 0) {
            // Sản phẩm thường
            $cartItem = CartItems::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->where('is_variant', 0)
                ->first();
        } else {
            // Sản phẩm biến thể (productId là ID của biến thể)
            $variant = ProductVariant::with('product')->findOrFail($productId);
            $productIdGoc = $variant->product_id;

            $cartItem = CartItems::where('cart_id', $cart->id)
                ->where('product_id', $productIdGoc)
                ->where('variant_id', $variant->id)
                ->where('is_variant', 1)
                ->first();
        }

        // dd($cartItem);

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            if ($cartItem->quantity > $stock) {
                 return redirect()->back()->with('error', 'Số lượng trong kho không đủ.');
            }
            else{
                $cartItem->save();
            }

        } else {
            CartItems::create([
                'cart_id'    => $cart->id,
                'product_id' => $product->id,     // vẫn cần biết sản phẩm gốc
                'variant_id' => $variant_id,     // đây là điểm quan trọng
                'quantity'   => $quantity,
                'price'      => $price,
                'is_variant' => $isVariant,
            ]);
        }

        return redirect()->route('list-cart')->with('success', 'Thêm vào giỏ hàng thành công.');
    }
    public function list()
    {
        // 1. Xác định giỏ hàng của người dùng
        if (Auth::check()) {
            $cart = Cart::with('items.product', 'items.variant')->where('user_id', Auth::id())->first();
        } else {
            $sessionId = session()->getId();
            $cart = Cart::with('items.product', 'items.variant')->where('session_id', $sessionId)->first();
        }

        // 2. Nếu chưa có giỏ hàng
        if (!$cart || $cart->items->isEmpty()) {
            return view('cart.list', ['cartItems' => [], 'total' => 0]);
        }

        // 3. Tính tổng tiền
        $total = 0;
        foreach ($cart->items as $item) {
            $total += $item->quantity * $item->price;
        }

        // 4. Trả về view
        return view('cart.list', [
            'cartItems' => $cart->items,
            'total' => $total
        ]);


    }
    public function update(Request $request)
{
    $item = CartItems::find($request->input('id'));

    if (!$item) {
        return response()->json(['error' => 'Không tìm thấy sản phẩm trong giỏ hàng'], 404);
    }

    $item->quantity = $request->input('quantity');
    $item->save();

    return response()->json(['success' => true]);
}
public function remove($id, Request $request){

    $cart =CartItems::find($id);
    if ( $cart ) {
        $cart->delete();
    }
    else {
         return redirect()->back()->with('error','Sản phẩm không tồn tại');
    }


    return redirect()->back()->with('success','Xóa giỏ hàng thành công');
}

}
