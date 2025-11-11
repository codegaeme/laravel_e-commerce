@extends('component.admin.layout.masterlayoutadmin')
@section('title')
    role
@endsection
@section('css')
    <style>
        .icon-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            transition: background-color 0.3s ease;
            color: #fafffb;
            /* Màu icon */
        }

        .icon-action:hover {
            background-color: #f0f0f0;
            /* Nền khi hover */
            color: #000;
        }

        .icon-action+.icon-action {
            margin-left: 8px;
            /* Khoảng cách giữa các icon */
        }

        .icon-action i {
            width: 18px;
            height: 18px;
        }
    </style>
@endsection
@section('js')
    <script>
        feather.replace();
    </script>
@endsection
@section('content')
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-xxl">
                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Danh sách quyền</h4>
                    </div>
                </div>
                <div class="row">
                    @include('component.alert')
                    <div class="col-12">
                        <div class="card ">
                            <div class="card-header col-sm-12 row">
                                <div class="col-sm-6">
                                    <a href="{{ route('admin.setRoles.create') }}" class="btn btn-primary btn-sm ">+
                                        Thêm
                                        quyền</a>
                                </div>
                                <div class="col-sm-6">
                                    <form method="GET" action="{{ route('admin.setRoles.index') }}"
                                        class="d-flex mb-3">

                                        <input type="text" name="keyword" class="form-control me-2"
                                            value="{{ request('keyword') }}" placeholder="Search...">



                                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                                    </form>
                                </div>


                            </div>

                            <div class="card-body">
                                <table id="fixed-columns-datatable"
                                    class="table table-striped nowrap row-border order-column w-100">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Tên</th>

                                            <th>Mô tả</th>

                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                        @foreach ($role as $key =>$cate)
                                            <tr>
                                                <td >{{ $key+1 }}</td>
                                                <td>{{ $cate->name }}</td>

                                                <td>{{ $cate->description ? $cate->description : 'Không có mô tả' }}
                                                </td>

                                                <td class=" align-middle">
                                                    <a href="{{ route('admin.setRoles.edit', $cate->id) }}"
                                                        class="icon-action text-success" title="Sửa">
                                                        <i data-feather="edit"></i>
                                                    </a>

                                                    <form action="{{ route('admin.setRoles.destroy',$cate->id) }}"
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
                                    <!-- Pagination -->
                                    {{ $role->links() }}
                                </table>
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
