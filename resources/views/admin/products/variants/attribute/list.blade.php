@extends('component.admin.layout.masterlayoutadmin')
@section('title')
    Attributes
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
                        <h4 class="fs-18 fw-semibold m-0">Danh sách Thuộc tính</h4>
                    </div>
                </div>
                <div class="row">
                    @include('component.alert')
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{ route('admin.products.variants.attributes.add') }}"
                                    class="btn btn-primary btn-sm">+ Thêm
                                    thuộc tính</a>
                            </div>

                            <div class="card-body">
                                <table class="table table-bordered p-3 m-4">
                                    <thead>
                                        <tr>
                                            <th>Tên</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $cate)
                                            <tr>
                                                <td>{{ $cate->name }}</td>

                                                <td class="text-center align-middle">
                                                    <!-- Nút xem giá trị -->
                                                    <button type="button" class="btn btn-info btn-sm mb-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#attributeValuesModal{{ $cate->id }}">
                                                        <i class="bi bi-eye-fill"></i> Xem giá trị
                                                    </button>

                                                    <!-- Nút thêm giá trị -->
                                                    <a href="{{ route('admin.products.variants.attributes.value.store', $cate->id) }}"
                                                        class="btn btn-success btn-sm">
                                                        <i class="bi bi-pencil-fill"></i> Thêm giá trị
                                                    </a>
                                                    <a href="{{ route('admin.products.variants.attributes.edit', $cate->id) }}"
                                                        class="btn btn-warning btn-sm">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                    <form action="{{ route('admin.products.variants.attributes.delete') }}"
                                                        method="POST" style="display:inline-block;"
                                                        onsubmit="return confirm('Bạn có chắc muốn xoá không?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" value="{{ $cate->id }}" name="id">
                                                        <button type="submit" class="icon-action text-danger"
                                                            title="Xoá" style="border: none; background: none;">
                                                            <i data-feather="trash-2"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>

                                <!-- MODALS -->
                                @foreach ($data as $cate)
                                    <div class="modal fade" id="attributeValuesModal{{ $cate->id }}" tabindex="-1"
                                        aria-labelledby="attributeValuesModalLabel{{ $cate->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Giá trị thuộc tính - {{ $cate->name }}</h5>

                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Đóng"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @if ($cate->values && $cate->values->count())
                                                        @foreach ($cate->values as $attribute)
                                                            <div class="row">
                                                                <h6 class="mt-3 col-6 text-center"><strong>{{ $attribute->value }}</strong>
                                                                </h6>
                                                                <div class="col-6 mt-3 text-center"><a href="{{ route('admin.products.variants.attributes.edit', $attribute->id) }}"
                                                                    class="icon-action text-success">
                                                                    <i class="bi bi-pencil-fill"></i>
                                                                </a>
                                                                <form
                                                                    action="{{ route('admin.products.variants.attributes.delete') }}"
                                                                    method="POST" style="display:inline-block;"
                                                                    onsubmit="return confirm('Bạn có chắc muốn xoá không?');" class="">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" value="{{ $attribute->id }}"
                                                                        name="id">
                                                                    <button type="submit" class="icon-action text-danger"
                                                                        title="Xoá"
                                                                        style="border: none; background: none;">
                                                                        <i data-feather="trash-2"></i>
                                                                    </button>
                                                                </form></div>

                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <p class="text-muted">Chưa có thuộc tính nào cho danh mục này.</p>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Đóng</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Pagination -->
                                {{ $data->links() }}
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
