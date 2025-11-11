@extends('component.admin.layout.masterlayoutadmin')
@section('title')
    Detail Products
@endsection
@section('css')
    <style>
        /* Container for thumbnail and badge to enable relative positioning for the badge */
        .image-thumbnail-container {
            position: relative;
            display: inline-block;
            /* margin-bottom to separate from the text below */
            margin-bottom: 1rem;
        }

        /* Styling for the main product thumbnail */
        .product-thumbnail {
            max-width: 80px;
            cursor: pointer;
            border-radius: 5px;
            display: block;
        }

        /* Styling for the image count badge (+X) */
        .image-count-badge {
            position: absolute;
            top: 0;
            /* Changed from 5px to 0 for better positioning within 80px thumbnail */
            right: 0;
            /* Changed from 5px to 0 */
            transform: translate(50%, -50%);
            /* Move the badge outside/above the corner */

            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 12px;
            font-weight: bold;
            user-select: none;
            /* Shadow for better visibility */
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
        }

        /* Center carousel images and make them responsive */
        .carousel-item img {
            max-height: 400px;
            /* Limit height for large screens */
            width: auto;
            /* Maintain aspect ratio */
        }
    </style>
@endsection
@section('js')
@endsection
@section('content')
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-xxl">
                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Chi tiết Sản Phẩm</h4>
                    </div>
                </div>
                <div class="row">
                    @include('component.alert')
                    <div class="col-12">
                        <div class="card">


                            <div class="card-body">
                                {{-- Refactored: Sử dụng container và class CSS để định vị badge --}}
                                @if ($product->thumbnail)
                                    <div class="image-thumbnail-container">
                                        <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}"
                                            class="product-thumbnail" data-bs-toggle="modal"
                                            data-bs-target="#imagesModal-{{ $product->id }}">

                                        @php
                                            $countImages = $product->images ? $product->images->count() : 0;
                                        @endphp

                                        @if ($countImages > 0)
                                            <span class="image-count-badge"
                                                title="Có {{ $countImages }} ảnh chi tiết khác, bấm vào xem thêm">
                                                +{{ $countImages }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span>Chưa có ảnh</span>
                                @endif
                                {{-- End Refactored Image Section --}}

                                {{-- Modal for Detail Images --}}
                                <div class="modal fade" id="imagesModal-{{ $product->id }}" tabindex="-1"
                                    aria-labelledby="imagesModalLabel-{{ $product->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <!-- modal-lg để rộng hơn -->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="imagesModalLabel-{{ $product->id }}">Ảnh chi
                                                    tiết
                                                    sản phẩm: {{ $product->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Đóng"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if ($product->images && $product->images->count() > 0)
                                                    <div id="carousel-{{ $product->id }}" class="carousel slide"
                                                        data-bs-ride="carousel">
                                                        <div class="carousel-inner text-center">
                                                            @foreach ($product->images as $key => $image)
                                                                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                                                    {{-- Đã loại bỏ width="200" và thêm class img-fluid --}}
                                                                    <img src="{{ Storage::url($image->image) }}"
                                                                        class="img-fluid"
                                                                        alt="Ảnh chi tiết {{ $key + 1 }}">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button class="carousel-control-prev" type="button"
                                                            data-bs-target="#carousel-{{ $product->id }}"
                                                            data-bs-slide="prev">
                                                            <span class="carousel-control-prev-icon"
                                                                aria-hidden="true"></span>
                                                            <span class="visually-hidden">Trước</span>
                                                        </button>
                                                        <button class="carousel-control-next" type="button"
                                                            data-bs-target="#carousel-{{ $product->id }}"
                                                            data-bs-slide="next">
                                                            <span class="carousel-control-next-icon"
                                                                aria-hidden="true"></span>
                                                            <span class="visually-hidden">Sau</span>
                                                        </button>
                                                    </div>
                                                @else
                                                    <p>Không có ảnh chi tiết.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- End Modal --}}

                                <p>Tên sản phẩm : <strong>{{ $product->name }}</strong></p>

                                <p>Tên danh mục : <strong>{{ $product->category->name_cate }}</strong></p>
                                <p>Mô tả ngắn : {{ $product->decription_short }}</p>
                                <p>Mô tả dài: {!! $product->description !!}</p> {{-- Sử dụng {!! !!} để hiển thị HTML từ mô tả dài (nếu có) --}}

                                @if ($product->is_variant == 0)
                                    <p>Giá: <strong>{{ number_format($product->price, 0, ',', '.') }} VNĐ</strong></p>
                                    @if ($product->price_sale !== null && $product->price_sale >= 0)
                                        <p>Giá khuyến mãi: <strong>{{ number_format($product->price_sale, 0, ',', '.') }}
                                                VNĐ</strong></p>
                                    @endif
                                    <p>Số lượng còn lại: <strong>{{ $product->stook }}</strong></p>
                                @endif
                                @if ($product->is_variant == 1)
                                    <h6 class="mt-4 mb-3">Danh sách các biến thể:</h6>
                                    <table class="table table-bordered table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th>STT</th>
                                                <th>Biến thể</th>
                                                <th>Hình ảnh</th>
                                                <th>Giá</th>
                                                <th>Giá khuyến mãi</th>
                                                <th>Số lượng</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($product->variants as $key => $item)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        @foreach ($item->values as $value)
                                                            <span
                                                                class="badge bg-primary me-1">{{ $value->attributeValue->value }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @if ($item->image)
                                                            <img src="{{ Storage::url($item->image) }}" alt="Variant Image"
                                                                height="50px" style="border-radius: 3px;">
                                                        @else
                                                            <span>Không có ảnh</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($item->price, 0, ',', '.') }} VNĐ</td>
                                                    <td>
                                                        @if ($item->sale_price !== null && $item->sale_price > 0)
                                                            <strong
                                                                class="text-danger">{{ number_format($item->sale_price, 0, ',', '.') }}
                                                                VNĐ</strong>
                                                        @else
                                                            <span>Không có</span>
                                                        @endif
                                                    </td>
                                                    <td><strong>{{ $item->quantity }}</strong></td>
                                                    <td>
                                                        {{-- Các nút Sửa/Xóa Biến thể đã được comment, giữ nguyên để tham khảo --}}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">Chưa có biến thể nào được thêm.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- container-fluid -->
        </div> <!-- content -->
        <!-- Footer Start -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col fs-13 text-muted text-center">
                        &copy;
                        <script>
                            document.write(new Date().getFullYear())
                        </script> - Made with <span class="mdi mdi-heart text-danger"></span> by <a
                            href="#!" class="text-reset fw-semibold">Zoyothemes</a>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->

    </div>
@endsection
