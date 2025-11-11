@extends('component.admin.layout.masterlayoutadmin')
@section('title')
    Update
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
                        <h4 class="fs-18 fw-semibold m-0">Sửa quyền</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="card">
                        <div class="container py-4">


                            <form action="{{ route('admin.setRoles.update', $category->id) }}" method="POST">
                                @csrf
                                @method('PUT') <!-- Dùng method PUT cho update -->

                                <div class="mb-3">
                                    <label for="name_cate" class="form-label">Tên quyền</label>
                                    <input type="text" name="name" id="name_cate"
                                        class="form-control @error('name_cate') is-invalid @enderror"
                                        value="{{ old('name', $category->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả</label>
                                    <textarea name="description" id="description" class="form-control">{{ old('description', $category->description) }}</textarea>
                                </div>



                                <button type="submit" class="btn btn-success">Lưu danh mục</button>
                                <a href="{{ route('admin.setRoles.index') }}" class="btn btn-secondary">Quay lại</a>
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
