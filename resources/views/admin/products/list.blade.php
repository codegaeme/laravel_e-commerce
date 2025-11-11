@extends('component.admin.layout.masterlayoutadmin')
@section('title')
    List Products
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
                        <h4 class="fs-18 fw-semibold m-0">Danh sách Sản Phẩm</h4>
                    </div>
                </div>
                <div class="row">
                    @include('component.alert')
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">

                                <a href="{{ route('admin.products.createSimple') }}" class="btn btn-primary btn-sm">+ Thêm
                                    sản phẩm</a>
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">+ Thêm
                                    sản phẩm biến thể</a>
                            </div>

                            <div class="card-body">
                                <table class="table table-striped">

                                    <thead>
                                        <tr>
                                            <th>STT</th>

                                            <th>Ảnh đại diện</th>
                                            <th>Danh mục sản phẩm</th>
                                            <th>Tên sản phẩm</th>

                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($products as $key => $product)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td style="position: relative; width: 100px;">
                                                    @if ($product->thumbnail)
                                                        <img src="{{ Storage::url($product->thumbnail) }}"
                                                            alt="{{ $product->name }}"
                                                            style="max-width: 80px; cursor: pointer; border-radius: 5px;"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#imagesModal-{{ $product->id }}">

                                                        @php
                                                            $countImages = $product->images
                                                                ? $product->images->count()
                                                                : 0;
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
                                                </td>
                                                <!-- Modal Carousel Ảnh chi tiết -->
                                                <div class="modal fade" id="imagesModal-{{ $product->id }}" tabindex="-1"
                                                    aria-labelledby="imagesModalLabel-{{ $product->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <!-- modal-lg để rộng hơn -->
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="imagesModalLabel-{{ $product->id }}">Ảnh chi tiết
                                                                    sản phẩm: {{ $product->name }}</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Đóng"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                @if ($product->images && $product->images->count() > 0)
                                                                    <div id="carousel-{{ $product->id }}"
                                                                        class="carousel slide" data-bs-ride="carousel">
                                                                        <div class="carousel-inner text-center">
                                                                            @foreach ($product->images as $key => $image)
                                                                                <div
                                                                                    class="carousel-item {{ $key == 0 ? 'active' : '' }}">
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

                                                <td>{{ $product->category->name_cate }}</td>
                                                <td>{{ $product->name }}</td>



                                                <td>
                                                    @if ($product->status)
                                                        <span class="badge bg-success">Hoạt động</span>
                                                    @else
                                                        <span class="badge bg-secondary">Không hoạt động</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                                        class="btn btn-sm btn-primary">Sửa</a>

                                                    <form action="{{ route('admin.products.delete') }}"
                                                        method="POST" style="display:inline-block"
                                                        onsubmit="return confirm('Bạn có chắc muốn xoá sản phẩm này?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="id" value="{{ $product->id }}">
                                                        <button type="submit" class="btn btn-sm btn-danger">Xoá</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Chưa có sản phẩm nào.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                {{-- Phân trang --}}
                                <div>
                                    {{ $products->links() }}
                                </div>
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
