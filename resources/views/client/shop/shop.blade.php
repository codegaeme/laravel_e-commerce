@extends('component.client.layout.masterlayoutsclient')
@section('title')
    Mẫu
@endsection
@section('css')
    {{-- Nếu bạn muốn tùy chỉnh thêm CSS cho bộ lọc, hãy thêm vào đây --}}
    <style>
        .filter-section {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }
        .filter-option-item a.active,
        .filter-option-item .form-check-input:checked ~ .form-check-label {
            font-weight: 600;
            color: #fff !important; /* Màu chữ trắng cho mục active */
            background-color: #007bff; /* Thay thế bằng màu theme chính của bạn */
            border-radius: 4px;
        }
        .filter-option-item a {
            padding: 8px 12px !important;
        }
        .filter-toggle-btn {
            cursor: pointer;
            user-select: none;
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-radius: 25px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .filter-toggle-btn:hover {
            border-color: #000;
        }
    </style>
@endsection
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('filter-toggle');
            const filterArea = document.getElementById('filter-options-area');

            // Khởi tạo trạng thái ban đầu: nếu có bất kỳ bộ lọc nào đang được áp dụng, mở bộ lọc.
            const hasActiveFilter = window.location.search.includes('category=') || window.location.search.includes('price_range=');
            if (hasActiveFilter) {
                filterArea.style.display = 'block';
                toggleButton.classList.add('active'); // Thêm class active nếu cần CSS styling cho nút
            }

            // Xử lý sự kiện nhấp vào nút toggle
            toggleButton.addEventListener('click', function() {
                if (filterArea.style.display === 'block' || filterArea.style.display === '') {
                    filterArea.style.display = 'none';
                    this.classList.remove('active');
                } else {
                    filterArea.style.display = 'block';
                    this.classList.add('active');
                }
            });
        });
    </script>
@endsection
@section('content')
    <section class="bg0 p-t-23 p-b-130">
        <div class="container">
            <!-- HEADER VÀ NÚT TOGGLE BỘ LỌC -->
            <div class="p-b-10">
                <h3 class="ltext-103 cl5">
                    Sản Phẩm Của Chúng Tôi
                </h3>
            </div>

            <div class="flex-w flex-sb-m p-b-52">
                {{-- Nút Toggle Bộ Lọc --}}
                <div class="filter-toggle-btn flex-w flex-c-m stext-106 cl6 size-104 bor4 pointer hov-btn3 trans-04 m-r-8 m-tb-4"
                     id="filter-toggle">
                    <i class="icon-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-filter-list"></i>
                    Bộ Lọc (Mở/Đóng)
                </div>

                {{-- Thêm các bộ sắp xếp khác nếu cần (ví dụ: Sắp xếp theo Giá/Tên) --}}
                {{-- <div class="flex-w flex-c-m m-tb-4">...</div> --}}
            </div>

            <!-- KHU VỰC TÙY CHỌN BỘ LỌC (Ẩn/Hiện) -->
            <div class="panel-filter w-full filter-section mb-5" id="filter-options-area" style="display: none;">
                <form method="GET" action="{{ route('shop') }}" id="filterForm">
                    <div class="row">
                        <!-- Lọc theo Danh mục (Cột 1) -->
                        <div class="col-md-6 col-lg-3 p-b-20">
                            <h6 class="text-uppercase fw-bold mb-3 stext-106 cl6">Danh mục</h6>
                            <div class="list-group list-group-flush stext-105 cl3">
                                {{-- Nút Tắt Lọc Danh mục --}}
                                <div class="filter-option-item">
                                    <a href="{{ route('shop', ['price_range' => request('price_range')]) }}"
                                       class="list-group-item list-group-item-action border-0 px-2 {{ !request('category') ? 'active' : '' }}">
                                        Tất cả sản phẩm
                                    </a>
                                </div>
                                @foreach ($categories as $category)
                                   
                                    <div class="filter-option-item">
                                        <a href="{{ route('shop', ['category' => $category->id, 'price_range' => request('price_range')]) }}"
                                           class="list-group-item list-group-item-action border-0 px-2 {{ request('category') == $category->id ? 'active bg-info text-white' : '' }}">
                                            {{ $category->name_cate }}
                                        </a>
                                    </div>


                                @endforeach
                            </div>
                        </div>

                        <!-- Lọc theo Khoảng giá (Cột 2) -->
                        <div class="col-md-6 col-lg-4 p-b-20">
                            <h6 class="text-uppercase fw-bold mb-3 stext-106 cl6">Khoảng Giá</h6>
                            @php
                                $priceRanges = [
                                    '0-500000' => 'Dưới 500.000 VNĐ',
                                    '500000-1000000' => '500.000 - 1.000.000 VNĐ',
                                    '1000000-5000000' => '1.000.000 - 5.000.000 VNĐ',
                                    '5000000-99999999' => 'Trên 5.000.000 VNĐ',
                                ];
                            @endphp

                            <div class="form-check m-b-10">
                                <input class="form-check-input" type="radio" name="price_range" value="" id="price_all"
                                       {{ !request('price_range') ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                <label class="form-check-label stext-105 cl3" for="price_all">Tất cả</label>
                            </div>

                            @foreach ($priceRanges as $value => $label)
                                <div class="form-check m-b-10">
                                    <input class="form-check-input" type="radio" name="price_range" value="{{ $value }}" id="price_{{ $loop->index }}"
                                           {{ request('price_range') == $value ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                    <label class="form-check-label stext-105 cl3" for="price_{{ $loop->index }}">{{ $label }}</label>
                                </div>
                            @endforeach

                            <a href="{{ route('shop') }}" class="btn btn-outline-danger w-full mt-3 stext-103">Xóa Bộ Lọc</a>

                        </div>

                        {{-- Bạn có thể thêm các bộ lọc khác (Ví dụ: Màu sắc, Kích cỡ) vào các cột còn lại --}}
                    </div>
                </form>
            </div>

            <!-- KHU VỰC SẢN PHẨM -->
            <div class="row isotope-grid">
                @forelse ($products as $item)
                    <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item women">
                        <!-- Block2 -->
                        <div class="block2">
                            <div class="block2-pic hov-img0 lable-new" data-label="New">
                                {{-- Sử dụng placeholder nếu ảnh không có --}}
                                <img src="{{ $item->thumbnail ? Storage::url($item->thumbnail) : 'https://placehold.co/400x400/808080/ffffff?text=No+Image' }}"
                                     alt="IMG-PRODUCT" height="250px" style="object-fit: cover;">

                                <a href="{{ route('product.detail', $item->id) }}"
                                    class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-show-modal1">
                                    Xem chi tiết
                                </a>
                            </div>

                            <div class="block2-txt flex-w flex-t p-t-14">
                                <div class="block2-txt-child1 flex-col-l ">
                                    <a href="{{ route('product.detail', $item->id) }}"
                                        class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                        {{ $item->name }}
                                    </a>

                                    <span class="mtext-106 cl2">
                                        @if ($item->is_variant === 0)
                                            @if ($item->price_sale !== null && $item->price_sale > 0)
                                                {{ number_format($item->price_sale, 0, ',', '.') }} VNĐ
                                                <del class="stext-105 cl3" style="font-size: 0.8em; margin-left: 5px;">{{ number_format($item->price, 0, ',', '.') }} VNĐ</del>
                                            @else
                                                {{ number_format($item->price ?? 0, 0, ',', '.') }} VNĐ
                                            @endif
                                        @else
                                            @php
                                                $prices = $item->variants->map(function ($variant) {
                                                    return $variant->price_sale && $variant->price_sale > 0
                                                        ? $variant->price_sale
                                                        : $variant->price ?? 0;
                                                });
                                                $minPrice = $prices->min();
                                                $maxPrice = $prices->max();
                                            @endphp
                                            @if ($minPrice !== null && $maxPrice !== null && $item->variants->isNotEmpty())
                                                {{ number_format($minPrice, 0, ',', '.') }} -
                                                {{ number_format($maxPrice, 0, ',', '.') }} VNĐ
                                            @else
                                                Liên hệ
                                            @endif
                                        @endif
                                    </span>

                                </div>

                                <div class="block2-txt-child2 flex-r p-t-3">
                                    <a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
                                        {{-- Giữ nguyên code yêu thích của bạn --}}
                                        <img class="icon-heart1 dis-block trans-04"
                                            src="{{ asset('client/assets/images/icons/icon-heart-01.png') }}"
                                            alt="ICON">
                                        <img class="icon-heart2 dis-block trans-04 ab-t-l"
                                            src="{{ asset('client/assets/images/icons/icon-heart-02.png') }}"
                                            alt="ICON">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 p-t-50 p-b-50">
                        <div class="alert alert-warning text-center stext-104 cl4">
                            Không tìm thấy sản phẩm nào phù hợp với bộ lọc hiện tại.
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- PHÂN TRANG -->
            @if ($products->hasPages())
                <div class="flex-c-m flex-w w-full p-t-38">
                    {{-- Giữ nguyên các tham số lọc khi chuyển trang --}}
                    {{ $products->appends(request()->input())->links('pagination::bootstrap-4') }}
                    {{-- Lưu ý: Bạn cần đảm bảo file pagination::bootstrap-4.blade.php tồn tại trong resources/views/vendor/pagination/ --}}
                </div>
            @endif
        </div>
    </section>
@endsection
