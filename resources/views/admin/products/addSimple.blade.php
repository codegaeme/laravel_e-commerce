@extends('component.admin.layout.masterlayoutadmin')
@section('title')
    Add Products
@endsection

@section('css')
    <style>
        .image-wrapper {
            position: relative;
            display: inline-block;
            margin-right: 10px;
        }

        .image-wrapper img {
            width: 100px;
            /* Kích thước ảnh album */
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .image-wrapper button {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            line-height: 10px;
            padding: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Style cho thumbnail chính */
        #thumbnailPreviewWrapper .image-wrapper img {
            width: 150px;
            height: 150px;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
    <script>
        window.addEventListener("load", function() {
            // ... Logic khởi tạo ảnh cũ/album hiện tại ...

            // Khởi tạo CKEditor cho textarea có ID là 'descriptionEditor'
            ClassicEditor
                .create(document.querySelector('#descriptionEditor'))
                .catch(error => {
                    console.error(error);
                });
        });
        const thumbnailInput = document.getElementById('thumbnailInput');
        const thumbnailWrapper = document.getElementById('thumbnailPreviewWrapper');
        const oldThumbnailPathInput = document.getElementById('oldThumbnailPath');

        const imageInput = document.getElementById('imageInput');
        const imageContainer = document.getElementById('imagePreviewContainer');
        const oldImagesPathInput = document.getElementById('oldImagesPath');

        let selectedImages = [];
        let temporaryImagePaths = [];

        // --- Hàm Tiện ích ---
        function createRemoveButton(onClickAction) {
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.textContent = 'X';
            removeBtn.onclick = onClickAction;
            return removeBtn;
        }

        // --- Xử lý Thumbnail (Ảnh chính) ---
        function renderThumbnail(file) {
            thumbnailWrapper.innerHTML = '';
            if (file) {
                const wrapper = document.createElement('div');
                wrapper.classList.add('image-wrapper');

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);

                const removeBtn = createRemoveButton(() => {
                    thumbnailInput.value = '';
                    oldThumbnailPathInput.value = '';
                    thumbnailWrapper.innerHTML = '';
                });

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                thumbnailWrapper.appendChild(wrapper);
            }
        }

        function renderThumbnailFromPath(path) {
            thumbnailWrapper.innerHTML = '';
            if (path) {
                const wrapper = document.createElement('div');
                wrapper.classList.add('image-wrapper');

                const img = document.createElement('img');
                // Đường dẫn trả về từ Storage::url() thường là tuyệt đối (bắt đầu bằng /storage/...)
                img.src = path;

                const removeBtn = createRemoveButton(() => {
                    oldThumbnailPathInput.value = ''; // Xóa đường dẫn cũ
                    thumbnailInput.value = '';
                    thumbnailWrapper.innerHTML = '';
                });

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                thumbnailWrapper.appendChild(wrapper);
            }
        }

        thumbnailInput.addEventListener('change', function(e) {
            oldThumbnailPathInput.value = ''; // Khi chọn file mới, xóa path cũ
            renderThumbnail(e.target.files[0]);
        });

        // --- Xử lý Album Hình ảnh ---
        imageInput.addEventListener('change', function(e) {
            const newFiles = Array.from(e.target.files);

            newFiles.forEach(file => {
                const existsInNew = selectedImages.some(f => f.name === file.name && f.size === file.size);
                if (!existsInNew) selectedImages.push(file);
            });

            e.target.value = ''; // Xóa giá trị của input file

            renderImagePreviews();
        });

        function renderImagePreviews() {
            imageContainer.innerHTML = '';

            // 1. Hiển thị ảnh cũ từ đường dẫn tạm thời
            // Duyệt qua mảng copy để tránh lỗi index khi xóa
            temporaryImagePaths.slice().forEach((path, originalIndex) => {
                const wrapper = document.createElement('div');
                wrapper.classList.add('image-wrapper');

                const img = document.createElement('img');
                img.src = path;

                const removeBtn = createRemoveButton(() => {
                    // Xóa item trong mảng gốc
                    const indexToRemove = temporaryImagePaths.findIndex(p => p === path);
                    if (indexToRemove !== -1) {
                        temporaryImagePaths.splice(indexToRemove, 1);
                    }

                    // Cập nhật lại trường ẩn
                    oldImagesPathInput.value = JSON.stringify(temporaryImagePaths);
                    renderImagePreviews();
                });

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                imageContainer.appendChild(wrapper);
            });

            // 2. Hiển thị ảnh mới từ đối tượng File
            selectedImages.slice().forEach((file, originalIndex) => {
                const wrapper = document.createElement('div');
                wrapper.classList.add('image-wrapper');

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);

                const removeBtn = createRemoveButton(() => {
                    // **SỬA LỖI Ở ĐÂY:** Dùng filter để xóa file an toàn hơn
                    selectedImages = selectedImages.filter((f, i) => i !== originalIndex);

                    renderImagePreviews();
                });

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                imageContainer.appendChild(wrapper);
            });

            // Cập nhật Input File
            updateImageInput();
        }

        function updateImageInput() {
            const dataTransfer = new DataTransfer();
            selectedImages.forEach(file => dataTransfer.items.add(file));
            imageInput.files = dataTransfer.files;
        }

        // --- Hàm Khởi tạo khi Tải Trang ---
        window.addEventListener("load", function() {
            // 1. Khởi tạo Album ảnh từ đường dẫn cũ (old data)
            if (oldImagesPathInput && oldImagesPathInput.value) {
                try {
                    temporaryImagePaths = JSON.parse(oldImagesPathInput.value);
                } catch (e) {
                    temporaryImagePaths = [];
                }
            }

            // 2. Khởi tạo Thumbnail
            if (oldThumbnailPathInput && oldThumbnailPathInput.value) {
                renderThumbnailFromPath(oldThumbnailPathInput.value);
            } else if (thumbnailInput.files.length > 0) {
                renderThumbnail(thumbnailInput.files[0]);
            }

            // 3. Hiển thị tất cả ảnh album (cũ và mới)
            renderImagePreviews();
        });
    </script>
@endsection

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-xxl">
                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Thêm mới Sản Phẩm (TEST)</h4>
                    </div>
                </div>
                <div class="row">
                    @include('component.alert')
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body" id="cardSimple">
                                <div class="container py-4">
                                    {{-- Đảm bảo action là route testpost --}}
                                    <form action="{{ route('admin.products.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-sm-12 row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="name_cate" class="form-label">Tên sản phẩm</label>
                                                    <input type="text" name="name" id="name_cate"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        value="{{ old('name') }}">
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Giá sản phẩm</label>
                                                    <input type="number" name="price"
                                                        class="form-control @error('price') is-invalid @enderror"
                                                        value="{{ old('price') }}">
                                                    @error('price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Giá khuyến mãi</label>
                                                    <input type="number" name="price_sale"
                                                        class="form-control @error('price_sale') is-invalid @enderror"
                                                        value="{{ old('price_sale') }}">
                                                    @error('price_sale')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Số lượng sản phẩm</label>
                                                    <input type="number" name="stook"
                                                        class="form-control @error('stook') is-invalid @enderror"
                                                        value="{{ old('stook') }}">
                                                    @error('stook')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Mô tả ngắn</label>
                                                    <input type="text" name="decription_short"
                                                        class="form-control @error('decription_short') is-invalid @enderror"
                                                        value="{{ old('decription_short') }}">
                                                    @error('decription_short')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Mô tả dài</label>
                                                    {{-- THÊM ID: "descriptionEditor" --}}
                                                    <textarea name="description" id="descriptionEditor" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label d-block">Hiển thị lên trang chủ?</label>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="status"
                                                            value="1"
                                                            {{ old('status', '1') == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label">Hiển thị</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="status"
                                                            value="0" {{ old('status') == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label">Ẩn</label>
                                                    </div>
                                                    @error('status')
                                                        <div class="text-danger small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                {{-- Giả định biến $categories được truyền từ view --}}
                                                <div class="mb-3">
                                                    <label for="category_id" class="form-label">Danh mục sản phẩm</label>
                                                    <select name="category_id" id="category_id"
                                                        class="form-select @error('category_id') is-invalid @enderror">
                                                        <option value="">-- Chọn danh mục --</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name_cate }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('category_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- THUMBNAIL INPUT --}}
                                                <div class="col-sm-6">
                                                    {{-- THUMBNAIL INPUT --}}
                                                    <div class="mt-3">
                                                        <label>Ảnh sản phẩm chính:</label><br>
                                                        <input type="file" name="thumbnail" id="thumbnailInput"
                                                            accept="image/*"
                                                            class="form-control @error('thumbnail') is-invalid @enderror">

                                                        {{-- **TRƯỜNG ẨN LƯU PATH CŨ** --}}
                                                        {{-- Lấy đường dẫn đã flash từ Controller: Session::get('thumbnail_path') hoặc old('thumbnail_path') --}}
                                                        <input type="hidden" name="old_thumbnail_path"
                                                            id="oldThumbnailPath"
                                                            value="{{ Session::get('thumbnail_path') ?? old('thumbnail_path') }}">

                                                        <div id="thumbnailPreviewWrapper" style="margin-top: 10px;"></div>
                                                        @error('thumbnail')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- ALBUM INPUT --}}
                                                    <div class="mt-3">
                                                        <label>Album hình ảnh:</label><br>
                                                        <input type="file" name="images[]" id="imageInput"
                                                            accept="image/*" multiple
                                                            class="form-control @error('images.*') is-invalid @enderror">

                                                        {{-- **TRƯỜNG ẨN LƯU MẢNG PATH CŨ (JSON ENCODED)** --}}
                                                        @php
                                                            // Ưu tiên lấy từ Session flash data (nếu có), nếu không lấy old(), mặc định là mảng rỗng
                                                            $oldImagesPathData =
                                                                Session::get('images_path') ?? old('images_path');
                                                            $oldImages = is_array($oldImagesPathData)
                                                                ? json_encode($oldImagesPathData)
                                                                : '[]';
                                                        @endphp
                                                        <input type="hidden" name="old_images_path" id="oldImagesPath"
                                                            value="{{ $oldImages }}">

                                                        <div id="imagePreviewContainer"
                                                            style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;">
                                                        </div>
                                                        @error('images.*')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="type_pro" value="0">
                                        <button type="submit" class="btn btn-primary btn-sm mt-3"> + Thêm sản phẩm

                                        </button>
                                        <a href="#" class="btn btn-secondary btn-sm mt-3">Quay lại</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
