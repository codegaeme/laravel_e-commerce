@extends('component.admin.layout.masterlayoutadmin')
@section('title')
    Detail Products
@endsection
@section('css')
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
                                @if ($product->thumbnail)
                                    <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}"
                                        style="max-width: 80px; cursor: pointer; border-radius: 5px;" data-bs-toggle="modal"
                                        data-bs-target="#imagesModal-{{ $product->id }}">

                                    @php
                                        $countImages = $product->images ? $product->images->count() : 0;
                                    @endphp

                                    @if ($countImages > 0)
                                        <span
                                            style="
                                                                        position: absolute;
                                                                        top: 5px;
                                                                        right: 5px;
                                                                        background-color: rgba(0, 0, 0, 0.7);
                                                                        color: white;
                                                                        font-size: 12px;
                                                                        padding: 2px 6px;
                                                                        border-radius: 12px;
                                                                        font-weight: bold;
                                                                        user-select: none;
                                                                    "
                                            title="Có {{ $countImages }} ảnh chi tiết khác, bấm vào xem thêm">
                                            +{{ $countImages }}
                                        </span>
                                    @endif
                                @else
                                    <span>Chưa có ảnh</span>
                                @endif

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
                                                                    <img src="{{ Storage::url($image->image) }}"
                                                                        class=""
                                                                        alt="Ảnh chi tiết {{ $key + 1 }}"
                                                                        width="200">
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
                                <p>Tên sản phẩm : {{ $product->name }}</p>

                                <p>Tên danh mục : {{ $product->category->name_cate }}</p>
                                <p>Mô tả ngắn : {{ $product->decription_short }}</p>
                                <p>Mô tả dài: {{ $product->description }}</p>

                                @if ($product->is_variant == 0)
                                    <p>Giá: {{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
                                    @if ($product->price_sale !== null || $product->price_sale == 0)
                                        <p>Giá khuyến mãi: {{ number_format($product->price_sale, 0, ',', '.') }} VNĐ</p>
                                    @endif
                                    Số lượng còn lại: {{ $product->stook }}
                                @endif
                                @if ($product->is_variant == 1)
                                    <p>Danh sách các biến thể: </p>
                                    <table class="table">
                                        <thead>
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
                                                          <p style="text-decoration: none ">{{$value->attributeValue->value}}</p>

                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @if ($item->image)
                                                            <img src="{{ Storage::url($item->image) }}" alt="Variant Image"
                                                               height="50px">
                                                        @else
                                                            <span>Không có ảnh</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($item->price, 0, ',', '.') }} VNĐ</td>
                                                    <td>
                                                        @if ($item->sale_price !== null && $item->sale_price > 0)
                                                            {{ number_format($item->sale_price, 0, ',', '.') }} VNĐ
                                                        @else
                                                            <span>Không có</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>
                                                        {{-- <a href="{{ route('admin.variants.edit', [$product->id, $item->id]) }}"
                                                            class="btn btn-sm btn-primary">Sửa</a>
                                                        <form
                                                            action="{{ route('admin.variants.destroy', [$product->id, $item->id]) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Xóa biến thể này?')">Xóa</button>
                                                        </form> --}}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">Chưa có biến thể</td>
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
