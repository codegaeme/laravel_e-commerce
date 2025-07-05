@extends('component.client.layout.masterlayoutsclient')

@section('title')
    Chi tiết đơn hàng {{ $order->order_code ?? '' }}
@endsection

@section('css')
    <style>
        body {
            background: #f8f9fa;
            font-size: 14px;
        }

        .back {
            background: #f1f3f5 !important;
            font-size: 14px;
        }

        .order-detail-box {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 12px;
            border-bottom: 1px dashed #dee2e6;
            padding-bottom: 6px;
        }

        .product-line {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 12px 0;
            gap: 12px;
        }

        .product-line:last-child {
            border-bottom: none;
        }

        .product-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .product-info {
            flex-grow: 1;
        }

        .product-name {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .variant-values {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 4px;
        }

        .variant-box {
            padding: 4px 10px;
            border: 1px solid #0d6efd;
            border-radius: 6px;
            color: #0d6efd;
            background-color: #f3f8ff;
            font-size: 12px;
        }

        .product-price {
            min-width: 120px;
            text-align: right;
            font-size: 13px;
            line-height: 1.6;
            color: #333;
        }

        .order-meta {
            font-size: 13px;
            color: #666;
            margin-bottom: 4px;
        }

        .total-line {
            text-align: right;
            font-size: 15px;
            font-weight: 600;
            color: #d63384;
        }

        .badge {
            font-size: 12px;
            padding: 5px 8px;
            border-radius: 5px;
        }

        .btn-back {
            font-size: 14px;
            text-decoration: none;
        }

        .note-box {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            font-size: 13px;
        }
    </style>
@endsection

@section('content')
    <section class="back">
        <div class="container py-4">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">📦 Chi tiết đơn hàng #{{ $order->order_code }}</h5>
                <a href="{{ route('myOrder') }}" class="btn-back text-primary">← Quay lại</a>
            </div>

            <!-- Thông tin đơn hàng & Địa chỉ -->
            <div class="order-detail-box">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-title">Thông tin đơn hàng</div>
                        <div class="order-meta">Mã đơn hàng: <strong>{{ $order->order_code }}</strong></div>
                        <div class="order-meta">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</div>
                        <div class="order-meta">
                            Trạng thái:
                            @php
                                $statuses = [
                                    'pending' => ['Chờ xác nhận', 'bg-warning text-dark'],
                                    'confirmed' => ['Đã xác nhận', 'bg-primary text-white'],
                                    'preparing' => ['Đang chuẩn bị hàng', 'bg-info text-dark'],
                                    'shipped' => ['Đã bàn giao vận chuyển', 'bg-secondary text-white'],
                                    'in_transit' => ['Đang giao hàng', 'bg-primary text-white'],
                                    'delivered' => ['Đã giao hàng', 'bg-success text-white'],
                                    'success' => ['Giao thành công', 'bg-success text-white'],
                                    'failed' => ['Giao thất bại', 'bg-danger text-white'],
                                    'canceled' => ['Đã hủy', 'bg-dark text-white'],
                                    'returned' => ['Trả hàng', 'bg-danger text-white'],
                                ];
                            @endphp
                            <span class="badge {{ $statuses[$order->status][1] ?? 'bg-secondary' }}">
                                {{ $statuses[$order->status][0] ?? 'Không xác định' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="section-title">Địa chỉ giao hàng</div>
                        <div class="order-meta">Tên: {{ $order->customer_name }}</div>
                        <div class="order-meta">SĐT: {{ $order->customer_phone }}</div>
                        <div class="order-meta">Địa chỉ: {{ $order->customer_address }}</div>
                    </div>

                </div>
                      @if ($order->note)
                <div class="order-detail-box mt-4">
                    <div class="section-title">Ghi chú</div>
                    <div class="note-box">
                        {{ $order->note }}
                    </div>
                </div>
            @endif
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="order-detail-box">
                <div class="section-title">Sản phẩm đã đặt</div>

                @foreach ($order->orderDetails as $orderDetail)
                    <div class="product-line">
                        <img src="{{ Storage::url($orderDetail->product->thumbnail) }}" class="product-image"
                            alt="{{ $orderDetail->product->name }}" />

                        <div class="product-info">
                            <div class="product-name">{{ $orderDetail->product->name }}</div>

                            @if ($orderDetail->variant_id)
                                <div class="variant-values">
                                    @foreach (\App\Models\Admin\ProductVariantValue::where('variant_id', $orderDetail->variant_id)->get() as $value)
                                        <span class="variant-box">{{ $value->value }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="product-price">
                            x {{ number_format($orderDetail->quantity) }}<br>
                            {{ number_format($orderDetail->price) }}đ<br>
                            <strong class="text-danger">{{ number_format($orderDetail->subtotal) }}đ</strong>
                        </div>
                    </div>
                @endforeach

                {{-- Tổng cộng --}}
                @php
                    $tongCong = $order->orderDetails->sum(fn($item) => $item->quantity * $item->price);
                @endphp
                <div class="total-line mt-3">
                    Tổng cộng: <span class="text-danger">{{ number_format($tongCong) }}đ</span>
                </div>
            </div>

            <!-- Tổng tiền & Ghi chú -->


            <!-- Ghi chú -->

            <!-- Tổng tiền -->
            @php
                $shippingFee = 30000; // Phí ship cố định
                $discount = 0; // Tạm thời không giảm giá
                $subtotal = $order->orderDetails->sum(function ($item) {
                    return $item->quantity * $item->price;
                });
                $grandTotal = $subtotal + $shippingFee - $discount;
            @endphp

            <!-- Chi tiết tổng tiền -->
            <div class="order-detail-box">
                <div class="section-title">Chi tiết thanh toán</div>
                <div class="d-flex justify-content-between py-1">
                    <span>Tạm tính:</span>
                    <span>{{ number_format($subtotal) }}đ</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span>Phí vận chuyển:</span>
                    <span>{{ number_format($shippingFee) }}đ</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span>Giảm giá:</span>
                    <span>-{{ number_format($discount) }}đ</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between py-1">
                    <strong>Tổng thanh toán:</strong>
                    <strong class="text-danger">{{ number_format($order->total_amount) }}đ</strong>
                </div>
            </div>
        </div>
    </section>
@endsection
