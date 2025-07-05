@extends('component.client.layout.masterlayoutsclient')

@section('title')
    Giỏ hàng
@endsection

@section('css')
<style>
    .qty-control {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .qty-control .qty {
        min-width: 30px;
        text-align: center;
        display: inline-block;
    }
    .qty-control button {
        width: 30px;
        height: 30px;
        background-color: #eee;
        border: 1px solid #ccc;
        cursor: pointer;
    }
</style>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formatCurrency = value => new Intl.NumberFormat('vi-VN').format(value) + ' đ';

        function updateCartItem(id, quantity, price, row) {
            const stock = parseInt(row.querySelector('.stock')?.textContent || 0);
            if (quantity > stock) {
                alert(`Số lượng vượt quá tồn kho! Chỉ còn ${stock} sản phẩm.`);
                return;
            }

            const total = quantity * price;
            row.querySelector('.qty').textContent = quantity;
            row.querySelector('.total-price').textContent = formatCurrency(total);

            fetch('/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id, quantity })
            })
            .then(res => res.json())
            .then(data => {
                console.log('Cập nhật thành công');
                updateCartTotals();
            })
            .catch(err => console.error('Lỗi cập nhật:', err));
        }

        function updateCartTotals() {
            const rows = document.querySelectorAll('.cart-item');
            let subtotal = 0;
            let totalQuantity = 0;

            rows.forEach(row => {
                const price = parseFloat(row.querySelector('.qty-control').dataset.price);
                const qty = parseInt(row.querySelector('.qty').textContent);
                subtotal += price * qty;
                totalQuantity += qty;
            });

            const total = subtotal;

            document.querySelector('.subtotal-text').textContent = formatCurrency(subtotal);
            document.querySelector('.total-text').textContent = formatCurrency(total);
            document.querySelector('.total-value').value = total;
            document.querySelector('.total-quantity').value = totalQuantity;
        }

        document.querySelectorAll('.qty-control').forEach(control => {
            const id = control.dataset.id;
            const price = parseFloat(control.dataset.price);
            const row = control.closest('.cart-item');

            control.querySelector('.btn-up').addEventListener('click', () => {
                let qty = parseInt(control.querySelector('.qty').textContent);
                qty++;
                updateCartItem(id, qty, price, row);
            });

            control.querySelector('.btn-down').addEventListener('click', () => {
                let qty = parseInt(control.querySelector('.qty').textContent);
                if (qty > 1) {
                    qty--;
                    updateCartItem(id, qty, price, row);
                }
            });
        });

        updateCartTotals();
    });
</script>
@endsection

@section('content')
<div class="container">
    <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
        <a href="/" class="stext-109 cl8 hov-cl1 trans-04">Home <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i></a>
        <span class="stext-109 cl4">Giỏ hàng</span>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-10 col-xl-8 m-lr-auto m-b-50">
            <div class="wrap-table-shopping-cart">
                <table class="table-shopping-cart">
                    <tr class="table_head">
                        <th class="column-1">Product</th>
                        <th class="column-2"></th>
                        <th class="column-2 text-center">Variant</th>
                        <th class="column-3 text-center">Price</th>
                        <th class="column-1 text-center">Quantity</th>
                        <th class="column-1 text-center">Total</th>
                        <th class="column-1">Remove</th>
                    </tr>

                    @foreach ($cartItems as $item)
                        @php
                            if ($item->is_variant == 0) {
                                $product = \App\Models\Admin\Product::with(['images'])->findOrFail($item->product_id);
                                $stock = $product->stook;
                            } else {
                                $variant = \App\Models\Admin\ProductVariant::with('product')->findOrFail($item->variant_id);
                                $product = $variant->product;
                                $stock = $variant->quantity;
                            }
                        @endphp
                        <tr class="table_row cart-item" data-id="{{ $item->id }}">
                            <td class="column-1">
                                <div class="how-itemcart1">
                                    <img src="{{ Storage::url($product->thumbnail) }}" alt="">
                                </div>
                            </td>
                            <td class="column-2">
                                {{ $product->name }}<br>
                                Còn lại: <span class="stock">{{ $stock }}</span>
                            </td>
                            <td class="column-2 text-center">
                                @if ($item->is_variant == 0)
                                    Không có biến thể
                                @else
                                    @foreach (\App\Models\Admin\ProductVariantValue::where('variant_id', $variant->id)->get() as $value)
                                        {{ $value->value }}<br>
                                    @endforeach
                                @endif
                            </td>
                            <td class="column-3 text-center">{{ number_format($item->price) }} đ</td>
                            <td class="column-1 text-center">
                                <div class="qty-control" data-id="{{ $item->id }}" data-price="{{ $item->price }}">
                                    <button type="button" class="btn-down">-</button>
                                    <span class="qty">{{ $item->quantity }}</span>
                                    <button type="button" class="btn-up">+</button>
                                </div>
                            </td>
                            <td class="column-1 text-center total-price">{{ number_format($item->price * $item->quantity) }} đ</td>
                            <td class="column-1">
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <form class="col-lg-10 col-xl-4 m-lr-auto m-b-50" action="{{ route('checkout') }}" method="post">
            @csrf
            <div class="bor10 p-lr-40 p-t-30 p-b-40">
                <h4 class="mtext-109 cl2 p-b-30">Cart Totals</h4>

                <div class="flex-w flex-t bor12 p-b-13">
                    <div class="size-208"><span class="stext-110 cl2">Subtotal:</span></div>
                    <div class="size-209"><span class="mtext-110 cl2 subtotal-text">0 đ</span></div>
                </div>

                <div class="flex-w flex-t p-t-27 p-b-33">
                    <div class="size-208"><span class="mtext-101 cl2">Total:</span></div>
                    <div class="size-209 p-t-1"><span class="mtext-110 cl2 total-text">0 đ</span></div>

                    <input type="hidden" class="total-value" name="total" value="">
                    <input type="hidden" class="total-quantity" name="qnt" value="">

                    @foreach ($cartItems as $item)
                        <input type="hidden" value="{{ $item->id }}" name="cartItem_id[]">
                    @endforeach
                </div>

                <button class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
                    Proceed to Checkout
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
