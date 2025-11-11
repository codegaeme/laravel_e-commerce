@extends('component.admin.layout.masterlayoutadmin')
@section('title')
    {{ $title }}
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
                        <h4 class="fs-18 fw-semibold m-0">{{ $title }} của thuộc tính {{ $att->name }}</h4>
                    </div>
                </div>
                <div class="row">
                    @include('component.alert')
                    <div class="col-12">
                        <div class="card ">
                            <div class="card-header col-sm-12 row">
                                <div class="col-sm-6">
                                    <a href="{{ route('admin.attributeValues.createSimple', $att->id) }}"
                                        class="btn btn-primary btn-sm ">+
                                        Thêm
                                        giá trị</a>
                                </div>
                                <div class="col-sm-6">
                                    <form method="GET" action="{{ route('admin.attributeValues.index') }}"
                                        class="d-flex mb-3">
                                        <input type="text" name="keyword" class="form-control me-2"
                                            value="{{ request('keyword') }}" placeholder="Search...">
                                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                                    </form>
                                </div>


                            </div>

                            <div class="card-body">
                                <table class="table table-striped mb-0">

                                    <thead class="thead-dark">
                                        <tr>
                                            <th>STT</th>
                                            <th>Giá trị thuộc tính</th>
                                            <th class="text-center">Tùy chọn</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($listAttValue as $key => $value)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $value->value }}</td>
                                                <td class="text-center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-light border btn-sm" type="button"
                                                            id="dropdownMenuButton{{ $value->id }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i data-feather="more-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end"
                                                            aria-labelledby="dropdownMenuButton{{ $value->id }}">
                                                            <li>
                                                                <a href="{{ route('admin.attributeValues.edit', ['attribute_id' => $att->id, 'id' => $value->id]) }}"
                                                                    class="dropdown-item">
                                                                    Sửa
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <form
                                                                    action="{{ route('admin.attributeValues.destroy', $value->id) }}"
                                                                    method="post"
                                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
                                                                    @csrf
                                                                    @method('delete')
                                                                    <input type="hidden" value="{{ $att->id }}"
                                                                        name="id_att">
                                                                    <button type="submit"
                                                                        class="dropdown-item text-danger">
                                                                        Xóa
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="mt-3"> {{ $listAttValue->links() }} </div>
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
