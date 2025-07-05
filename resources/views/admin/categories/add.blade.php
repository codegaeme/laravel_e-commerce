@extends('component.admin.layout.masterlayoutadmin')
@section('title')
    Add Categories
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
                        <h4 class="fs-18 fw-semibold m-0">Thêm danh mục sản phẩm</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="card">
                           <div class="container py-4">


                        <form action="{{ route('admin.categories.store-cate') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="name_cate" class="form-label">Tên danh mục</label>
                                <input type="text" name="name_cate" id="name_cate"
                                    class="form-control @error('name_cate') is-invalid @enderror"
                                    value="{{ old('name_cate') }}">
                                @error('name_cate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                            </div>

                            {{-- <div class="mb-3">
            <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
            <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
        </div> --}}

                            <div class="mb-3">
                                <label class="form-label d-block">Trạng thái</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status_show"
                                        value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status_show">Hiển thị</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status_hide"
                                        value="0" {{ old('status') == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status_hide">Ẩn</label>
                                </div>
                            </div>


                            <button type="submit" class="btn btn-success">Lưu danh mục</button>
                            <a href="{{ route('admin.categories.list-cate') }}" class="btn btn-secondary">Quay lại</a>
                        </form>

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
