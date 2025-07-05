@extends('component.admin.layout.masterlayoutadmin')
@section('title')
    Danh sách đơn hàng
@endsection
@section('css')
    <style>
        .back {
            background: #f1f3f5 !important;
            font-size: 14px;
        }

        .order-box {
            background: #fff;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: 0.3s ease;
        }

        .order-box:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .order-date {
            color: #6c757d;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .badge {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .btn-sm {
            padding: 4px 10px;
            font-size: 13px;
        }

        .highlight-price {
            color: #e83e8c;
            font-weight: bold;
        }

        .product-line {
            display: flex;
            align-items: start;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px dashed #dee2e6;
        }

        .product-line:last-child {
            border-bottom: none;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .product-info {
            flex-grow: 1;
        }

        .product-info .name {
            font-weight: 500;
            color: #333;
        }

        .product-info .meta {
            font-size: 13px;
            color: #666;
        }

        .order-footer {
            margin-top: 12px;

            padding-top: 10px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            /* Chia 3 cột bằng nhau */
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #ddd;
            font-size: 14px;
        }
    </style>
@endsection
@section('js')

@endsection
@section('content')
    <div class="content-page">
        <div class="content">
            <section class="back">
                <div class="container py-4">

                    @foreach ($orders as $order)
                        <div class="order-box">
                            <div class="order-header">
                                <strong class="text-primary">#DH00123</strong>

                                @if ($order->status == 'pending')
                                    <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                @elseif ($order->status == 'confirmed')
                                    <span class="badge bg-primary text-white">Đã xác nhận</span>
                                @elseif ($order->status == 'preparing')
                                    <span class="badge bg-info text-dark">Đang chuẩn bị hàng</span>
                                @elseif ($order->status == 'shipped')
                                    <span class="badge bg-secondary text-white">Đã bàn giao cho đơn vị vận chuyển</span>
                                @elseif ($order->status == 'in_transit')
                                    <span class="badge bg-primary text-white">Đang giao hàng</span>
                                @elseif ($order->status == 'delivered')
                                    <span class="badge bg-success">Đã giao hàng</span>
                                @elseif ($order->status == 'success')
                                    <span class="badge bg-success">Giao hàng thành công</span>
                                @elseif ($order->status == 'failed')
                                    <span class="badge bg-danger">Giao hàng thất bại</span>
                                @elseif ($order->status == 'canceled')
                                    <span class="badge bg-dark">Đã hủy</span>
                                @elseif ($order->status == 'returned')
                                    <span class="badge bg-danger">Trả hàng / Hoàn hàng</span>
                                @else
                                    <span class="badge bg-secondary">Không xác định</span>
                                @endif


                            </div>
                            <div class="order-date">Ngày đặt: {{ $order->created_at->format('d/m/Y') }}</div>
                            <!-- Sản phẩm -->
                            @foreach ($order->orderDetails as $orderDetail)
                                <div class="product-line">
                                    @if ($orderDetail->variant_id && $orderDetail->variant_id != null)
                                        <img src="{{ Storage::url($orderDetail->product->thumbnail) }}"
                                            class="product-image" alt="Áo thun" />
                                    @else
                                        <img src="{{ Storage::url($orderDetail->product->thumbnail) }}"
                                            class="product-image" alt="Áo thun" />
                                    @endif

                                    <div class="product-info">
                                        <div class="name">{{ $orderDetail->product->name }}</div>
                                        <div class="name">
                                            @if ($orderDetail->variant_id && $orderDetail->variant_id != null)
                                                @foreach (\App\Models\Admin\ProductVariantValue::where('variant_id', $orderDetail->variant_id)->get() as $value)
                                                    {{ $value->value }}
                                                @endforeach
                                            @endif

                                        </div>
                                        <div class="meta">x {{ number_format($orderDetail->quantity) }} •
                                            {{ number_format($orderDetail->price) }}đ =
                                            {{ number_format($orderDetail->subtotal) }}đ
                                        </div>

                                    </div>
                                </div>
                            @endforeach


                            <!-- Tổng + Thanh toán -->
                            <div class="order-footer">
                                <div>
                                    <div class="mb-1">Tổng: <span
                                            class="highlight-price">{{ number_format($order->total_amount) }}đ</span></div>
                                    <div>Thanh toán: <span class="badge bg-success">Đã thanh toán</span></div>
                                </div>
                                <div></div>
                                <div class="row g-2 mt-3 ">
                                    <div class="col-5 text-end">
                                        <a href="{{ route('myOrderDetail', ['id' => $order->id]) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                    @php
                                        $statusActions = [
                                            'pending' => [
                                                'confirmed',
                                                'Xác nhận đơn hàng',
                                                'primary',
                                                'Bạn chắc chắn muốn xác nhận đơn hàng này?',
                                            ],
                                            'confirmed' => [
                                                'preparing',
                                                'Chuẩn bị hàng',
                                                'secondary',
                                                'Bạn chắc chắn muốn chuẩn bị đơn hàng này?',
                                            ],
                                            'preparing' => [
                                                'shipped',
                                                'Chuyển qua đơn vị vận chuyển',
                                                'info',
                                                'Bạn chắc chắn muốn chuyển đơn hàng này qua đơn vị vận chuyển?',
                                            ],
                                            'shipped' => [
                                                'in_transit',
                                                'Nhận đơn hàng',
                                                'warning',
                                                'Bạn chắc chắn muốn nhận đơn hàng này?',
                                            ],
                                            'in_transit' => [
                                                'delivered',
                                                'Đã giao hàng',
                                                'success',
                                                'Bạn chắc chắn đã giao đơn hàng này?',
                                            ],
                                        ];

                                        $current = $order->status;
                                    @endphp

                                    <div class="col-7 text-start">
                                        @if (isset($statusActions[$current]))
                                            @php
                                                [$nextStatus, $label, $btnClass, $confirmMsg] = $statusActions[
                                                    $current
                                                ];
                                            @endphp

                                            <form method="post" action="{{ route('admin.update-status-orders') }}"
                                                onsubmit="return confirm('{{ $confirmMsg }}')">
                                                @csrf
                                                @method('put')
                                                <input type="hidden" name="status" value="{{ $current }}">
                                                <input type="hidden" name="id" value="{{ $order->id }}">
                                                <button
                                                    class="btn btn-outline-{{ $btnClass }} btn-sm">{{ $label }}</button>
                                            </form>
                                        @elseif ($order->status == 'canceled')
                                            <span class="btn btn-outline-danger btn-sm disabled">Đơn hàng đã bị hủy</span>
                                        @elseif ($order->status == 'delivered')
                                            <span class="btn btn-outline-success btn-sm disabled">Giao hàng
                                                thành công</span>
                                        @endif
                                    </div>



                                </div>




                            </div>
                        </div>
                    @endforeach
                    <!-- Đơn hàng 1 -->


                </div>
            </section>
        </div>
    </div>
@endsection
