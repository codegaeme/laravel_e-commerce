@extends('component.admin.layout.masterlayoutadmin')
@section('title')
    {{ $title }}
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
                        <h4 class="fs-18 fw-semibold m-0">{{ $title }}</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="card">
                        <div class="container py-4">
                            <form action="{{ route('admin.attributes.update', $att->id) }}" method="post" id="formNotEmpty">
                                @csrf
                                @method('PUT')
                                <div class=" p-3">
                                    <label for="inputAddCate">Tên thuộc tính :</label>
                                    <input type="text" id="inputNotEmpty" name="name"
                                        placeholder="Nhập tên thuộc tính" class="form-control mt-3"
                                        value="{{ old('name', $att->name ?? '') }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="p-3">
                                    <button type="submit" id="btnSubmit" class="btn btn-sm btn-primary">Cập nhật</button>
                                </div>
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
