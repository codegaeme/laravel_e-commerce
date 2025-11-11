@extends('layouts.admin.masterlayout')
@section('title')
    Danh sách thuộc tính
@endsection
@section('css')

@endsection
@section('js')
@endsection
@section('content')
    <div class="container mt-3 ">

        <div class="col-xl-12 mt-3">
            <div class="card">
                <div class="card-header row">
                    <div class="col-sm-6">
                        <h3>Danh sách thuộc tính</h3>
                    </div>
                    <div class="col-sm-6 text-end "><a href="{{ route('admin.attributes.create') }}"
                            class="btn btn-primary mb-3 "> Thêm mới thuộc tính</a></div>

                </div><!-- end card header -->

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">

                            <thead class="thead-dark">
                                <tr>
                                    <th>STT</th>
                                    <th>Tên thuộc tính</th>
                                    <th>Giá trị thuộc tính</th>
                                    <th class="text-center">Tùy chọn</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listAtt as $key => $value)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $value->name }}</td>
                                        <td>

                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-light border btn-sm" type="button"
                                                    id="dropdownMenuButton{{ $value->id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="dropdownMenuButton{{ $value->id }}">
                                                    <li>
                                                        <a href="{{ route('admin.attributes.edit', $value->id) }}"
                                                            class="dropdown-item">
                                                            Sửa
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.attributes.destroy', $value->id) }}"
                                                            method="post"
                                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
                                                            @csrf
                                                            @method('delete')
                                                           
                                                            <button type="submit" class="dropdown-item text-danger">
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
                        <div class="mt-3">
                            {{ $listAtt->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>




    </div>
@endsection
