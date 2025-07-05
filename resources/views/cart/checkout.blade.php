@extends('component.client.layout.masterlayoutsclient')

@section('title')
    Giỏ hàng
@endsection

@section('css')
    <style>
        header {
            z-index: 1030;
            /* thấp hơn modal (1050) */
        }

        .modal {
            z-index: 1100 !important;
        }

        .modal-backdrop {
            z-index: 1090 !important;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .bor10 {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            background-color: #fff;
            padding: 20px;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .cart-summary-table td img {
            width: 60px;
            height: auto;
            border-radius: 6px;
        }

        @media (max-width: 767px) {
            .row.m-b-50 {
                flex-direction: column;
            }

            .col-lg-6 {
                width: 100%;
                margin-bottom: 30px;
            }
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formatCurrency = value => new Intl.NumberFormat('vi-VN').format(value) + ' đ';

            let subtotal = 0;
            document.querySelectorAll('.cart-item').forEach(item => {
                const price = parseFloat(item.dataset.price);
                const qty = parseInt(item.dataset.qty);
                subtotal += price * qty;
            });

            const shipping = 30000;
            const discount = 0;
            const total = subtotal + shipping - discount;

            document.querySelector('.subtotal-text').textContent = formatCurrency(subtotal);
            document.querySelector('.shipping-fee-text').textContent = formatCurrency(shipping);
            document.querySelector('.discount-text').textContent = formatCurrency(discount);
            document.querySelector('.total-text').textContent = formatCurrency(total);
            document.querySelector('.total-value').value = total;
        });
    </script>
@endsection

@section('content')
    <div class="container">
        <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
            <a href="/" class="stext-109 cl8 hov-cl1 trans-04">Home
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>
            <span class="stext-109 cl4">Giỏ hàng</span>
        </div>
    </div>

    <div class="container">
        <form action="{{ route('checkoutPost') }}" method="POST">
            @csrf
            <div class="row m-b-50">
                {{-- LEFT - Địa chỉ giao hàng --}}
                <div class="col-lg-6">
                    <div class="bor10">
                        <!-- Nút mở modal -->
                        <button class="btn btn-primary btn-sm mb-3" type="button" data-bs-toggle="modal"
                            data-bs-target="#modalDiaChi">
                            Chọn địa chỉ giao hàng
                        </button>

                        <h4 class="mtext-109 cl2 p-b-20">Thông tin giao hàng</h4>
                        <div class="form-group">
                            <label for="fullname">Họ và tên</label>
                            <input type="text" name="fullname" id="fullname" value="{{ $shippingAddress->name ?? '' }}"
                                class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" name="phone" id="phone" value="{{ $shippingAddress->phone ?? '' }}"
                                class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="address">Địa chỉ chi tiết</label>
                            <textarea name="address" id="address" rows="3" class="form-control" value="{{ $shippingAddress->address ?? '' }}">{{ $shippingAddress->address ?? '' }}</textarea>
                        </div>
                          <div class="form-group">
                            <label for="note">Ghi chú</label>
                            <textarea name="note" id="address" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                {{-- RIGHT - Tóm tắt đơn hàng --}}
                <div class="col-lg-6">
                    <div class="bor10">
                        <h4 class="mtext-109 cl2 p-b-20">Tóm tắt đơn hàng</h4>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <tbody>
                                    @foreach ($cartItems as $item)
                                        @php
                                            $ship = 30000;
                                            $discount = 0;

                                            if ($item->is_variant == 0) {
                                                $product = \App\Models\Admin\Product::with(['images'])->findOrFail(
                                                    $item->product_id,
                                                );
                                            } else {
                                                $variant = \App\Models\Admin\ProductVariant::with(
                                                    'product',
                                                )->findOrFail($item->variant_id);
                                                $product = $variant->product;
                                            }
                                        @endphp
                                        <tr class="cart-item" data-price="{{ $item->price }}"
                                            data-qty="{{ $item->quantity }}">
                                            <td style="width: 60px">
                                                <img src="{{ Storage::url($product->thumbnail) }}"
                                                    alt="{{ $product->name }}" class="img-fluid rounded">
                                            </td>
                                            <td>
                                                <strong>{{ $product->name }}</strong><br>
                                                @if ($item->is_variant)
                                                    @foreach (\App\Models\Admin\ProductVariantValue::where('variant_id', $variant->id)->get() as $value)
                                                        <small>{{ $value->value }}</small>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($item->price) }} đ x {{ $item->quantity }}<br>
                                                <strong>{{ number_format($item->price * $item->quantity) }} đ</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span class="subtotal-text fw-bold">{{ $subtotal }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí giao hàng:</span>
                            <span class="shipping-fee-text fw-bold">{{ $ship }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Giảm giá:</span>
                            <span class="discount-text fw-bold">{{ $discount }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2 mt-2">
                            <span class="fw-bold">Tổng cộng:</span>
                            <span class="total-text fw-bold text-danger">{{ $subtotal + $ship + $discount }}</span>
                        </div>
                        <input type="hidden" name="subtotal" value="{{ $subtotal }}">
                        <input type="hidden" name="ship" value="{{ $ship }}">
                        <input type="hidden" name="discount" value="{{ $discount }}">

                        <input type="hidden" class="total-value" name="total"
                            value="{{ $subtotal + $ship + $discount }}">
                        @foreach ($cartItems as $item)
                            <input type="hidden" name="cartItem_id[]" value="{{ $item->id }}">
                        @endforeach

                        <div style="display: flex; gap: 30px; align-items: center;" class="mt-3">
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="radio" name="payment_method" value="cod" checked>
                                Thanh toán khi nhận hàng
                            </label>
                        </div>
                               <div style="display: flex; gap: 30px; align-items: center;" class="mt-1">
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="radio" name="payment_method" value="vnpay" >
                                Thanh toán bằng Vnpay
                            </label>
                        </div>




                        <div class="mt-4">
                            <button class="btn btn-success w-100">Đặt hàng</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Modal trống chọn địa chỉ --}}
    <div class="modal fade" id="modalDiaChi" tabindex="-1" aria-labelledby="modalDiaChiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalDiaChiLabel">Địa chỉ giao hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>

                @if (!empty($shippingAddresses) && $shippingAddresses->count() > 0)
                    <div class="modal-body">
                        @foreach ($shippingAddresses as $ship)
                            <div class="border rounded p-3 mb-2">
                                <div>
                                    <strong>{{ $ship->name }}</strong> - {{ $ship->phone }}<br>
                                    {{ $ship->address }}
                                </div>
                                <form action="{{ route('checkout') }}" method="POST" class="mt-2">
                                    @csrf
                                    <input type="hidden" class="total-value" name="total" value="">
                                    <input type="hidden" class="total-quantity" name="qnt" value="">

                                    @foreach ($cartItems as $item)
                                        <input type="hidden" value="{{ $item->id }}" name="cartItem_id[]">
                                    @endforeach
                                    <input type="hidden" name="address_id" value="{{ $ship->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Chọn địa chỉ
                                        này</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="modal-body">
                        <p>Chưa có địa chỉ giao hàng.</p>
                        <a href="{{ route('shippingAdddress') }}" class="btn btn-sm btn-primary mt-2">Thêm mới địa
                            chỉ</a>
                    </div>
                @endif



                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>

            </div>
        </div>
    </div>
@endsection
