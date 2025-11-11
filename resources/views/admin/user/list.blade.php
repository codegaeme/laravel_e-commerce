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
@section('content')
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-xxl">
                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Danh sách User</h4>
                    </div>
                </div>
                <div class="row">
                    @include('component.alert')
                    <div class="col-12">
                        <div class="card ">
                            <div class="card-header col-sm-12 row">
                                <div class="col-sm-6">
                                    {{-- ... Nội dung bên trái ... --}}
                                </div>
                                <div class="col-sm-6">
                                    <form method="GET" action="{{ route('admin.authen') }}" class="d-flex mb-3">
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
                                            <th>Vai trò hiện tại</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user as $key => $cate)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $cate->name }}</td>
                                                <td>{{ $cate->role->name ?? 'Chưa có vai trò' }}
                                                </td>
                                                <td class=" align-middle">
                                                    {{-- Nút Kích hoạt Modal --}}
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal" data-bs-target="#setRoleModal"
                                                        data-user-id="{{ $cate->id }}"
                                                        data-user-name="{{ $cate->name }}">
                                                        Set role
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- Pagination -->
                                {{ $user->links() }}
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

        <!-- Modal Form Set Role -->
        {{-- Tôi đã di chuyển modal ra ngoài cùng để tránh bị lặp lại trong vòng lặp @foreach --}}
        <div class="modal fade" id="setRoleModal" tabindex="-1" aria-labelledby="setRoleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="setRoleModalLabel">Đặt Vai Trò cho Người Dùng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Action sẽ được cập nhật bằng JS --}}
                    <form id="roleForm" method="POST" action="{{ route('admin.setRoles') }}">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="user_id" id="modal_user_id" value="">

                        <div class="modal-body">
                            <p>Bạn đang đặt vai trò cho User:
                                <strong id="display_user_name"></strong>
                                (<span class="text-muted" id="display_user_id"></span>)
                            </p>

                            <div class="mb-3">
                                <label for="role_select" class="form-label">Chọn Vai Trò Mới</label>
                                <select class="form-select" id="role_select" name="role_id" required>
                                    <option value="" selected disabled>-- Chọn vai trò --</option>

                                    {{-- Vòng lặp lấy danh sách vai trò từ biến $roles --}}
                                    {{-- LƯU Ý: Biến $roles cần được truyền từ Controller sang View --}}
                                    @if(isset($roles))
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    @else
                                        {{-- Trường hợp $roles chưa được truyền --}}
                                        <option disabled>Không tìm thấy danh sách vai trò ($roles)</option>
                                    @endif

                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Lưu Vai Trò</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script>
        feather.replace();

        // LOGIC JAVASCRIPT ĐIỀN DỮ LIỆU VÀO MODAL
        document.addEventListener('DOMContentLoaded', function () {
            const setRoleModal = document.getElementById('setRoleModal');

            // Bắt sự kiện khi modal sắp được hiển thị
            setRoleModal.addEventListener('show.bs.modal', function (event) {
                // Lấy nút đã kích hoạt modal (nút Set role)
                const button = event.relatedTarget;

                // Lấy data từ thuộc tính data-* của nút
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');

                // Cập nhật giá trị vào input ẩn trong form
                const modalUserIdInput = setRoleModal.querySelector('#modal_user_id');
                modalUserIdInput.value = userId;



                // Hiển thị tên và ID người dùng lên tiêu đề
                setRoleModal.querySelector('#display_user_name').textContent = userName;
                setRoleModal.querySelector('#display_user_id').textContent = '(ID: ' + userId + ')';
            });
        });
    </script>
@endsection
