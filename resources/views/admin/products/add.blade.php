@extends('component.admin.layout.masterlayoutadmin')
@section('title')
    Add Products
@endsection
@section('css')
    <style>
        .d-none {
            display: none !important;
        }

        /* Dùng class của Bootstrap để ẩn */
        .visually-hidden {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }

        .image-wrapper {
            position: relative;
            display: inline-block;
            margin-right: 10px;
        }

        .image-wrapper img {
            width: 100px;
            height: auto;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .image-wrapper button {
            position: absolute;
            top: 2px;
            right: 2px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 14px;
            line-height: 20px;
            cursor: pointer;
            padding: 0;
            text-align: center;
        }

        /* Thêm style cho nút active để dễ nhận biết */
        .attribute-btn.active {
            border-color: #007bff;
            background-color: #007bff;
            color: white !important;
        }

        /* Đảm bảo phần biến thể luôn hiển thị */
        .varriant {
            display: block !important;
        }
    </style>
@endsection
@section('js')
    {{-- //xử lí hình ảnh --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
    <script>
        // Khối JS xử lý hình ảnh (Giữ nguyên)
        window.addEventListener("load", function() {
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Lưu trữ thuộc tính và giá trị đã chọn
            let selectedAttributes = []; // Dùng để lưu trữ ID và Name của thuộc tính
            // Lưu trữ các giá trị đã chọn: { 'attrId': [{id: valueId, value: valueName}, ...], ... }
            let selectedAttributeValues = {};
            let currentAttributeId = null; // Biến mới để lưu ID thuộc tính đang mở modal

            // Xử lý lưu thuộc tính
            document.getElementById('saveAttributes').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.attribute-checkbox:checked');
                const selectedList = document.getElementById('selectedAttributes');
                const hiddenInput = document.getElementById('selected_attribute_ids');

                // Các khối DOM cần ẩn/hiện
                const attrWrapper = document.getElementById('selectedAttributesWrapper');
                const valuesWrapper = document.getElementById('selectedAttributesValuesWrapper');
                const btnWrapper = document.getElementById('generateVariantsBtnWrapper');

                selectedList.innerHTML = ''; // Xóa các nút cũ
                selectedAttributes = []; // Reset danh sách thuộc tính
                let selectedIds = [];

                // Lấy danh sách ID thuộc tính cũ để kiểm tra xem có thuộc tính nào bị hủy chọn không
                const oldAttributeIds = Object.keys(selectedAttributeValues);
                const newAttributeIds = Array.from(checkboxes).map(cb => cb.value);

                // Loại bỏ các giá trị thuộc tính của các thuộc tính đã bị hủy chọn
                oldAttributeIds.forEach(oldId => {
                    if (!newAttributeIds.includes(oldId)) {
                        delete selectedAttributeValues[oldId];
                    }
                });

                if (checkboxes.length > 0) {
                    attrWrapper.classList.remove('visually-hidden');
                    valuesWrapper.classList.remove('visually-hidden');
                    btnWrapper.classList.remove('visually-hidden');

                    checkboxes.forEach(function(checkbox) {
                        const attrName = checkbox.getAttribute('data-name');
                        const attrId = checkbox.value;

                        // Tạo nút thuộc tính
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.classList.add('btn', 'btn-outline-primary', 'btn-sm', 'attribute-btn', 'ms-2', 'mb-2');
                        btn.textContent = attrName;
                        btn.setAttribute('data-id', attrId);

                        // Xử lý bấm vào nút thuộc tính để mở modal giá trị
                        btn.addEventListener('click', async function() {
                            // 1. Cập nhật trạng thái active
                            document.querySelectorAll('.attribute-btn').forEach(b => b.classList.remove('active'));
                            btn.classList.add('active');

                            // 2. LƯU ID THUỘC TÍNH HIỆN TẠI
                            currentAttributeId = attrId;

                            document.getElementById('attributeValueModalLabel')
                                .textContent = `Giá trị của "${attrName}"`;

                            try {
                                const response = await fetch(
                                    `/api/attributevalues/${attrId}`, {
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector(
                                                'meta[name="csrf-token"]').content
                                        }
                                    });
                                if (!response.ok) throw new Error(
                                    `HTTP error! Status: ${response.status}`);
                                const attributeValues = await response.json();

                                let html = '';
                                if (!Array.isArray(attributeValues) || attributeValues.length === 0) {
                                    html = '<p>Không có giá trị thuộc tính nào.</p>';
                                } else {
                                    // Lấy danh sách ID giá trị đã chọn cho thuộc tính hiện tại
                                    // QUAN TRỌNG: Đồng bộ hóa trạng thái
                                    const selectedValueIds = (selectedAttributeValues[attrId] || []).map(v => String(v.id));

                                    attributeValues.forEach(function(value) {
                                        if (value.id && value.value) {
                                            // Kiểm tra nếu ID giá trị đã tồn tại trong mảng đã chọn
                                            const checked = selectedValueIds.includes(String(value.id)) ? 'checked' : '';

                                            html += `
                                            <div class="form-check">
                                                <input class="form-check-input attribute-value-checkbox" type="checkbox"
                                                    value="${value.id}" data-name="${value.value}" id="value_${value.id}" ${checked}>
                                                <label class="form-check-label" for="value_${value.id}">
                                                    ${value.value}
                                                </label>
                                            </div>
                                        `;
                                        }
                                    });
                                }

                                document.getElementById('attributeValuesContent')
                                    .innerHTML = html;

                                const modal = new bootstrap.Modal(document.getElementById(
                                    'attributeValueModal'));
                                modal.show();
                            } catch (error) {
                                console.error('Lỗi khi lấy giá trị thuộc tính:', error);
                                document.getElementById('attributeValuesContent')
                                    .innerHTML =
                                    '<p>Lỗi khi tải giá trị. Vui lòng thử lại.</p>';
                                const modal = new bootstrap.Modal(document.getElementById(
                                    'attributeValueModal'));
                                modal.show();
                            }
                        });

                        selectedList.appendChild(btn);
                        selectedIds.push(attrId);
                        selectedAttributes.push({
                            id: attrId,
                            name: attrName
                        });

                        // Khởi tạo mảng giá trị nếu chưa có
                        if (!selectedAttributeValues[attrId]) {
                            selectedAttributeValues[attrId] = [];
                        }
                    });

                } else {
                    // Nếu không có thuộc tính nào được chọn, ẩn lại
                    attrWrapper.classList.add('visually-hidden');
                    valuesWrapper.classList.add('visually-hidden');
                    btnWrapper.classList.add('visually-hidden');
                    document.getElementById('variantListWrapper').classList.add('visually-hidden');
                }

                hiddenInput.value = selectedIds.join(',');
            });

            // Xử lý lưu giá trị thuộc tính
            document.getElementById('saveAttributeValues').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('#attributeValuesContent .attribute-value-checkbox:checked');
                const selectedValuesList = document.getElementById('selectedAttributesValues');
                const hiddenValuesInput = document.getElementById('selected_attribute_values_ids');

                // SỬ DỤNG currentAttributeId đã lưu
                const attributeId = currentAttributeId;
                if (!attributeId) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('attributeValueModal'));
                    modal.hide();
                    return;
                }

                // 1. Xóa các nút giá trị cũ của thuộc tính này khỏi DOM
                selectedValuesList.querySelectorAll(`button[data-attribute-id="${attributeId}"]`).forEach(btn => btn.remove());

                // 2. Cập nhật trạng thái (object JS)
                selectedAttributeValues[attributeId] = [];
                let allSelectedValueIds = [];

                // 3. Thêm các nút giá trị mới vào DOM và cập nhật object JS
                checkboxes.forEach(function(checkbox) {
                    const valueName = checkbox.getAttribute('data-name');
                    const valueId = checkbox.value;

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.classList.add('btn', 'btn-outline-secondary', 'btn-sm', 'ms-2', 'mb-2');
                    btn.textContent = valueName;
                    btn.setAttribute('data-id', valueId);
                    btn.setAttribute('data-attribute-id', attributeId);

                    selectedValuesList.appendChild(btn);

                    selectedAttributeValues[attributeId].push({
                        id: valueId,
                        value: valueName
                    });
                });

                // 4. Cập nhật trường ẩn chứa TẤT CẢ Value ID
                Object.values(selectedAttributeValues).forEach(valueArray => {
                    valueArray.forEach(val => {
                        allSelectedValueIds.push(val.id);
                    });
                });

                hiddenValuesInput.value = [...new Set(allSelectedValueIds)].join(',');

                // 5. Đóng modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('attributeValueModal'));
                modal.hide();
            });

            // Xử lý nút Tạo biến thể
            document.getElementById('generateVariantsBtn').addEventListener('click', function() {
                // Thêm validation trước khi tạo biến thể
                if (!validateVariantGeneration()) {
                    return;
                }
                document.getElementById('variantListWrapper').classList.remove('visually-hidden');
                generateVariants();
            });

            // Hàm validation kiểm tra đã chọn thuộc tính và giá trị chưa
            function validateVariantGeneration() {
                const selectedAttrCount = Object.keys(selectedAttributeValues).length;

                let hasValues = false;
                for (const attrId in selectedAttributeValues) {
                    if (selectedAttributeValues[attrId] && selectedAttributeValues[attrId].length > 0) {
                        hasValues = true;
                        break;
                    }
                }

                if (selectedAttrCount === 0) {
                    alert('Vui lòng chọn ít nhất một Thuộc tính.');
                    return false;
                }

                if (!hasValues) {
                    alert('Các thuộc tính đã chọn phải có ít nhất một Giá trị thuộc tính.');
                    return false;
                }

                return true;
            }

            // Hàm tạo tổ hợp biến thể
            function generateVariants() {
                const variantList = document.getElementById('variantList');
                variantList.innerHTML = ''; // Xóa danh sách biến thể cũ

                // Lấy tất cả giá trị thuộc tính đã chọn (chỉ lấy các thuộc tính có giá trị)
                const attributeValues = Object.values(selectedAttributeValues).filter(values => values.length > 0);

                if (attributeValues.length === 0) {
                    variantList.innerHTML = '<p class="text-danger">Không đủ dữ liệu để tạo biến thể.</p>';
                    return;
                }

                // Tạo tổ hợp biến thể
                const combinations = generateCombinations(attributeValues);

                if (combinations.length === 0) {
                    variantList.innerHTML = '<p>Không có biến thể nào được tạo.</p>';
                    return;
                }

                // Render bảng biến thể
                let html = `
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Biến thể</th>
                                <th>Mã sản phẩm</th>
                                <th>Giá</th>
                                <th>Giá KM</th>
                                <th>SL</th>
                                <th>Hình ảnh</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                combinations.forEach((combo, index) => {
                    const variantName = combo.map(item => item.value).join(' / ');
                    const valueIds = combo.map(item => item.id).join(',');
                    html += `
                        <tr>
                            <td>${variantName}</td>
                            <td>
                                <input type="text" name="variants[${index}][sku]" class="form-control" placeholder="Mã sản phẩm" required pattern="[a-zA-Z0-9-]+" title="Mã SKU không chứa ký tự đặc biệt, chỉ chấp nhận chữ, số và dấu gạch ngang." >
                                <input type="hidden" name="variants[${index}][attribute_value_ids]" value="${valueIds}">
                            </td>
                            <td>
                                <input type="number" name="variants[${index}][price]" class="form-control" placeholder="Giá" required min="1">
                            </td>
                            <td>
                                <input type="number" name="variants[${index}][price_sale]" class="form-control" placeholder="Giá Khuyến mãi" min="0">
                            </td>
                            <td>
                                <input type="number" name="variants[${index}][stock]" class="form-control" placeholder="Số lượng" required min="1">
                            </td>
                            <td>
                                <input type="file" name="variants[${index}][image]" accept="image/*">
                            </td>
                        </tr>
                    `;
                });

                html += '</tbody></table>';
                variantList.innerHTML = html;
            }

            // Hàm tạo tổ hợp từ danh sách giá trị thuộc tính (Cartesian Product)
            function generateCombinations(arrays) {
                if (arrays.length === 0) return [];

                const firstArray = arrays[0].map(item => [item]);
                const remainingArrays = arrays.slice(1);

                return remainingArrays.reduce((acc, curr) => {
                    const result = [];
                    acc.forEach(a => {
                        curr.forEach(c => {
                            result.push([...a, c]);
                        });
                    });
                    return result;
                }, firstArray);
            }
        });
    </script>
@endsection
@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-xxl">
                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Thêm mới Sản Phẩm có biến thể</h4>
                    </div>
                </div>
                <div class="row">
                    {{-- @include('component.alert') --}}
                    <div class="col-12">
                        <div class="card">

                            <div class="card-body" id="cardSimple">
                                <div class="container py-4">
                                    <form action="{{ route('admin.products.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf

                                        {{-- TRƯỜNG ẨN CHO LOẠI SẢN PHẨM BIẾN THỂ (type_product = 1) --}}
                                        <input type="hidden" name="type_product" value="1">

                                        <div class="col-sm-12 row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="name_cate" class="form-label">Tên sản phẩm</label>
                                                    <input type="text" name="name" id="name_cate"
                                                        class="form-control @error('name_cate') is-invalid @enderror"
                                                        value="{{ old('name') }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="decription_short" class="form-label">Mô tả ngắn</label>
                                                    <input type="text" name="decription_short" id="decription_short"
                                                        class="form-control @error('decription_short') is-invalid @enderror"
                                                        value="{{ old('decription_short') }}" required>
                                                    @error('decription_short')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Mô tả dài</label>
                                                    <textarea name="description" id="descriptionEditor" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>



                                                <div class="mb-3">
                                                    <label class="form-label d-block">Hiển thị lên trang chủ không</label>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="status"
                                                            id="status_show" value="1"
                                                            {{ old('status', '1') == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="status_show">Hiển thị</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="status"
                                                            id="status_hide" value="0"
                                                            {{ old('status') == '0' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="status_hide">Ẩn</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="category_id" class="form-label">Danh mục sản phẩm</label>
                                                    <select name="category_id" id="category_id"
                                                        class="form-select @error('category_id') is-invalid @enderror" required>
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
                                                <div class="col-sm-12">
                                                    <div class="mt-3">
                                                        <label>Ảnh sản phẩm chính:</label><br>
                                                        <input type="file" name="thumbnail" id="thumbnailInput"
                                                            accept="image/*"
                                                            class="form-control @error('thumbnail') is-invalid @enderror" required>

                                                        {{-- **TRƯỜNG ẨN LƯU PATH CŨ** --}}
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
                                                            $oldImagesPathData =
                                                                Session::get('images_path') ?? old('images_path');
                                                            $oldImages = is_array($oldImagesPathData)
                                                                ? json_encode($oldImagesPathData)
                                                                : '[]';
                                                        @endphp
                                                        <input type="hidden" name="old_images_path" id="oldImagesPath"
                                                            value="{{ $oldImages }}">

                                                        <div id="imagePreviewContainer" style="margin-top: 10px;"></div>
                                                        @error('images.*')
                                                            <div class="invalid-feedback d-block">Lỗi album ảnh.</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                          {{-- KHỐI SẢN PHẨM BIẾN THỂ (VARRIANT) LUÔN HIỂN THỊ --}}
                                                <div class="varriant mb-3">
                                                    {{-- Nút chọn thuộc tính --}}
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal" data-bs-target="#attributeModal">
                                                        Chọn thuộc tính
                                                    </button>

                                                    {{-- KHỐI ẨN 1: Thuộc tính đã chọn --}}
                                                    <div class="mt-3 visually-hidden" id="selectedAttributesWrapper">
                                                        <strong>Thuộc tính đã chọn:</strong>
                                                        <div id="selectedAttributes" class="d-flex gap-2 flex-wrap mt-2">
                                                        </div>
                                                    </div>

                                                    {{-- KHỐI ẨN 2: Giá trị thuộc tính đã chọn --}}
                                                    <div class="mt-3 visually-hidden" id="selectedAttributesValuesWrapper">
                                                        <strong>Các giá trị thuộc tính đã chọn:</strong>
                                                        <div id="selectedAttributesValues"
                                                            class="d-flex gap-2 flex-wrap mt-2">
                                                            <input type="hidden" name="selected_attribute_values_ids"
                                                                id="selected_attribute_values_ids">
                                                            <input type="hidden" name="selected_attribute_ids"
                                                                id="selected_attribute_ids">
                                                        </div>
                                                    </div>

                                                    {{-- KHỐI ẨN 3: Nút tạo biến thể --}}
                                                    <div class="mt-3 visually-hidden" id="generateVariantsBtnWrapper">
                                                        <button type="button" class="btn btn-sm btn-success"
                                                            id="generateVariantsBtn">Tạo biến thể</button>
                                                    </div>

                                                    {{-- KHỐI ẨN 4: Khu vực hiển thị biến thể --}}
                                                    <div class="mt-3 visually-hidden" id="variantListWrapper">
                                                        <strong>Danh sách biến thể sản phẩm:</strong>
                                                        <div id="variantList" class="mt-2">
                                                            {{-- Danh sách biến thể sẽ được render động ở đây --}}
                                                        </div>
                                                    </div>
                                                </div>
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Chọn Thuộc tính --}}
    <div class="modal fade" id="attributeModal" tabindex="-1" aria-labelledby="attributeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attributeModalLabel">Chọn Thuộc tính Sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Giả định $attributes là mảng các thuộc tính (id, name,...) --}}
                    @foreach ($attributes as $attribute)
                        <div class="form-check">
                            <input class="form-check-input attribute-checkbox" type="checkbox"
                                value="{{ $attribute->id }}" data-name="{{ $attribute->name }}"
                                id="attribute_{{ $attribute->id }}">
                            <label class="form-check-label" for="attribute_{{ $attribute->id }}">
                                {{ $attribute->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="saveAttributes"
                        data-bs-dismiss="modal">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Chọn Giá trị Thuộc tính --}}
    <div class="modal fade" id="attributeValueModal" tabindex="-1" aria-labelledby="attributeValueModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attributeValueModalLabel">Giá trị của [Tên Thuộc tính]</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="attributeValuesContent">
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="saveAttributeValues">Lưu Giá trị</button>
                </div>
            </div>
        </div>
    </div>
@endsection
