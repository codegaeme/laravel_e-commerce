@extends('component.admin.layout.masterlayoutadmin')
@section('title')
    Add Products
@endsection
@section('css')
    <style>
        .d-none {
            display: none !important;
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
        }
    </style>
@endsection
@section('js')
    {{-- //xử lí hình ảnh  --}}
    <script>
        const thumbnailInput = document.getElementById('thumbnailInput');
        const thumbnailWrapper = document.getElementById('thumbnailPreviewWrapper');

        const imageInput = document.getElementById('imageInput');
        const imageContainer = document.getElementById('imagePreviewContainer');
        let selectedImages = []; // giữ danh sách ảnh chi tiết

        // Xử lý thumbnail
        thumbnailInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            thumbnailWrapper.innerHTML = '';
            if (file) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.maxWidth = '150px';
                img.style.marginRight = '10px';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.textContent = 'Xoá';
                removeBtn.onclick = () => {
                    thumbnailInput.value = '';
                    thumbnailWrapper.innerHTML = '';
                };

                thumbnailWrapper.appendChild(img);
                thumbnailWrapper.appendChild(removeBtn);
            }
        });

        // Xử lý ảnh chi tiết
        imageInput.addEventListener('change', function(e) {
            const newFiles = Array.from(e.target.files);
            newFiles.forEach(file => {
                const exists = selectedImages.some(f => f.name === file.name && f.size === file.size);
                if (!exists) {
                    selectedImages.push(file);
                }
            });
            // giữ ảnh cũ + thêm ảnh mới
            renderImagePreviews();
        });

        function renderImagePreviews() {
            imageContainer.innerHTML = '';

            selectedImages.forEach((file, index) => {
                const wrapper = document.createElement('div');
                wrapper.style.position = 'relative';
                wrapper.style.marginRight = '10px';

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.maxWidth = '100px';
                img.style.border = '1px solid #ccc';
                img.style.borderRadius = '6px';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.textContent = 'X';
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '0';
                removeBtn.style.right = '0';
                removeBtn.style.background = 'red';
                removeBtn.style.color = 'white';
                removeBtn.style.border = 'none';
                removeBtn.style.borderRadius = '50%';
                removeBtn.style.width = '20px';
                removeBtn.style.height = '20px';
                removeBtn.style.cursor = 'pointer';

                removeBtn.onclick = () => {
                    selectedImages.splice(index, 1);
                    renderImagePreviews(); // render lại preview
                };

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                imageContainer.appendChild(wrapper);
            });

            updateImageInput();
        }

        function updateImageInput() {
            const dataTransfer = new DataTransfer();
            selectedImages.forEach(file => dataTransfer.items.add(file));
            imageInput.files = dataTransfer.files;
        }
    </script>

    {{-- xử lí loại sản phẩmphẩm --}}
    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            const select = document.getElementById("type_product");
            const simpleForm = document.querySelector(".simble");
            const variantForm = document.querySelector(".varriant");

            function toggleForms() {
                if (select.value === "0") {
                    simpleForm.style.display = "block";
                    variantForm.style.display = "none";
                } else {
                    simpleForm.style.display = "none";
                    variantForm.style.display = "block";
                }
            }

            // Khi thay đổi lựa chọn
            select.addEventListener("change", toggleForms);

            // Gọi khi trang load lần đầu
            toggleForms();
        });
    </script> --}}
    {{-- Hiện thuộc tính --}}

    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Chuyển đổi giữa form đơn giản và biến thể
            const select = document.getElementById("type_product");
            const simpleForm = document.querySelector(".simble");
            const variantForm = document.querySelector(".varriant");

            function toggleForms() {
                if (select.value === "0") {
                    simpleForm.style.display = "block";
                    variantForm.style.display = "none";
                } else {
                    simpleForm.style.display = "none";
                    variantForm.style.display = "block";
                }
            }

            select.addEventListener("change", toggleForms);
            toggleForms();

            // Xử lý lưu thuộc tính
            document.getElementById('saveAttributes').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.attribute-checkbox:checked');
                const selectedList = document.getElementById('selectedAttributes');
                const hiddenInput = document.getElementById('selected_attribute_ids');

                selectedList.innerHTML = ''; // Xóa các nút cũ
                let selectedIds = [];

                checkboxes.forEach(function(checkbox) {
                    const attrName = checkbox.getAttribute('data-name');
                    const attrId = checkbox.value;

                    // Tạo nút thuộc tính
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.classList.add('btn', 'btn-outline-primary', 'btn-sm', 'attribute-btn');
                    btn.textContent = attrName;
                    btn.setAttribute('data-id', attrId);

                    // Xử lý bấm vào nút thuộc tính để mở modal giá trị
                    btn.addEventListener('click', async function() {
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

                            // Kiểm tra dữ liệu
                            let html = '';
                            if (!Array.isArray(attributeValues) || attributeValues
                                .length === 0) {
                                html = '<p>Không có giá trị thuộc tính nào.</p>';
                            } else {
                                attributeValues.forEach(function(value) {
                                    // Sử dụng value.value thay vì value.name
                                    if (value.id && value.value) {
                                        html += `
                                    <div class="form-check">
                                        <input class="form-check-input attribute-value-checkbox" type="checkbox"
                                            value="${value.id}" data-name="${value.value}" id="value_${value.id}">
                                        <label class="form-check-label" for="value_${value.id}">
                                            ${value.value}
                                        </label>
                                    </div>
                                `;
                                    } else {
                                        console.warn(
                                            'Dữ liệu giá trị không hợp lệ:',
                                            value);
                                    }
                                });
                            }

                            document.getElementById('attributeValuesContent')
                                .innerHTML = html;

                            // Hiển thị modal
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
                });

                hiddenInput.value = selectedIds.join(',');
            });

            // Xử lý lưu giá trị thuộc tính
            document.getElementById('saveAttributeValues').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.attribute-value-checkbox:checked');
                const selectedValuesList = document.getElementById('selectedAttributesValues');
                const hiddenValuesInput = document.getElementById('selected_attribute_values_ids');
                let selectedValueIds = hiddenValuesInput.value ? hiddenValuesInput.value.split(',') : [];

                // Lấy attribute_id từ nút thuộc tính đang active
                const activeAttributeBtn = document.querySelector('.attribute-btn.active');
                const attributeId = activeAttributeBtn ? activeAttributeBtn.getAttribute('data-id') : null;

                // Xóa các nút giá trị cũ của thuộc tính này
                const existingValueButtons = selectedValuesList.querySelectorAll(
                    `button[data-attribute-id="${attributeId}"]`);
                existingValueButtons.forEach(btn => btn.remove());

                checkboxes.forEach(function(checkbox) {
                    const valueName = checkbox.getAttribute('data-name');
                    const valueId = checkbox.value;

                    // Tạo nút giá trị
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.classList.add('btn', 'btn-outline-secondary', 'btn-sm');
                    btn.textContent = valueName;
                    btn.setAttribute('data-id', valueId);
                    btn.setAttribute('data-attribute-id', attributeId);

                    selectedValuesList.appendChild(btn);
                    if (!selectedValueIds.includes(valueId)) {
                        selectedValueIds.push(valueId);
                    }
                });

                hiddenValuesInput.value = selectedValueIds.join(',');

                // Đóng modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('attributeValueModal'));
                modal.hide();
            });

            // Thêm class active khi bấm vào nút thuộc tính
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('attribute-btn')) {
                    document.querySelectorAll('.attribute-btn').forEach(btn => btn.classList.remove(
                        'active'));
                    e.target.classList.add('active');
                }
            });
        });
    </script> --}}
    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Chuyển đổi giữa form đơn giản và biến thể
            const select = document.getElementById("type_product");
            const simpleForm = document.querySelector(".simble");
            const variantForm = document.querySelector(".varriant");

            function toggleForms() {
                if (select.value === "0") {
                    simpleForm.style.display = "block";
                    variantForm.style.display = "none";
                } else {
                    simpleForm.style.display = "none";
                    variantForm.style.display = "block";
                }
            }

            select.addEventListener("change", toggleForms);
            toggleForms();

            // Lưu trữ thuộc tính và giá trị đã chọn
            let selectedAttributes = [];
            let selectedAttributeValues = {};

            // Xử lý lưu thuộc tính
            document.getElementById('saveAttributes').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.attribute-checkbox:checked');
                const selectedList = document.getElementById('selectedAttributes');
                const hiddenInput = document.getElementById('selected_attribute_ids');

                selectedList.innerHTML = ''; // Xóa các nút cũ
                selectedAttributes = []; // Reset danh sách thuộc tính
                let selectedIds = [];

                checkboxes.forEach(function(checkbox) {
                    const attrName = checkbox.getAttribute('data-name');
                    const attrId = checkbox.value;

                    // Tạo nút thuộc tính
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.classList.add('btn', 'btn-outline-primary', 'btn-sm', 'attribute-btn');
                    btn.textContent = attrName;
                    btn.setAttribute('data-id', attrId);

                    // Xử lý bấm vào nút thuộc tính để mở modal giá trị
                    btn.addEventListener('click', async function() {
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
                            if (!Array.isArray(attributeValues) || attributeValues
                                .length === 0) {
                                html = '<p>Không có giá trị thuộc tính nào.</p>';
                            } else {
                                attributeValues.forEach(function(value) {
                                    if (value.id && value.value) {
                                        html += `
                                    <div class="form-check">
                                        <input class="form-check-input attribute-value-checkbox" type="checkbox"
                                            value="${value.id}" data-name="${value.value}" id="value_${value.id}">
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
                });

                hiddenInput.value = selectedIds.join(',');
                generateVariants(); // Tạo lại biến thể khi thuộc tính thay đổi
            });

            // Xử lý lưu giá trị thuộc tính
            document.getElementById('saveAttributeValues').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.attribute-value-checkbox:checked');
                const selectedValuesList = document.getElementById('selectedAttributesValues');
                const hiddenValuesInput = document.getElementById('selected_attribute_values_ids');
                let selectedValueIds = hiddenValuesInput.value ? hiddenValuesInput.value.split(',') : [];

                const activeAttributeBtn = document.querySelector('.attribute-btn.active');
                const attributeId = activeAttributeBtn ? activeAttributeBtn.getAttribute('data-id') : null;

                // Xóa các nút giá trị cũ của thuộc tính này
                const existingValueButtons = selectedValuesList.querySelectorAll(
                    `button[data-attribute-id="${attributeId}"]`);
                existingValueButtons.forEach(btn => btn.remove());

                // Lưu giá trị thuộc tính
                selectedAttributeValues[attributeId] = [];
                checkboxes.forEach(function(checkbox) {
                    const valueName = checkbox.getAttribute('data-name');
                    const valueId = checkbox.value;

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.classList.add('btn', 'btn-outline-secondary', 'btn-sm');
                    btn.textContent = valueName;
                    btn.setAttribute('data-id', valueId);
                    btn.setAttribute('data-attribute-id', attributeId);

                    selectedValuesList.appendChild(btn);
                    if (!selectedValueIds.includes(valueId)) {
                        selectedValueIds.push(valueId);
                    }
                    selectedAttributeValues[attributeId].push({
                        id: valueId,
                        value: valueName
                    });
                });

                hiddenValuesInput.value = selectedValueIds.join(',');

                const modal = bootstrap.Modal.getInstance(document.getElementById('attributeValueModal'));
                modal.hide();

                generateVariants(); // Tạo lại biến thể khi giá trị thuộc tính thay đổi
            });

            // Thêm class active khi bấm vào nút thuộc tính
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('attribute-btn')) {
                    document.querySelectorAll('.attribute-btn').forEach(btn => btn.classList.remove(
                        'active'));
                    e.target.classList.add('active');
                }
            });

            // Hàm tạo tổ hợp biến thể
            function generateVariants() {
                const variantList = document.getElementById('variantList');
                variantList.innerHTML = ''; // Xóa danh sách biến thể cũ

                // Lấy tất cả giá trị thuộc tính đã chọn
                const attributeValues = Object.values(selectedAttributeValues).filter(values => values.length > 0);
                if (attributeValues.length === 0) {
                    variantList.innerHTML = '<p>Chưa chọn giá trị thuộc tính nào.</p>';
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
                        <th>Giá</th>
                        <th>Số lượng</th>
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
                        <input type="number" name="variants[${index}][price]" class="form-control" placeholder="Giá" required>
                        <input type="hidden" name="variants[${index}][attribute_value_ids]" value="${valueIds}">
                    </td>
                    <td>
                        <input type="number" name="variants[${index}][stock]" class="form-control" placeholder="Số lượng" required>
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

            // Hàm tạo tổ hợp từ danh sách giá trị thuộc tính
            function generateCombinations(arrays) {
                if (arrays.length === 0) return [];
                return arrays.reduce((acc, curr) => {
                    const result = [];
                    acc.forEach(a => {
                        curr.forEach(c => {
                            result.push([...(Array.isArray(a) ? a : [a]), c]);
                        });
                    });
                    return result;
                }, [
                    []
                ]);
            }
        });
    </script> --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Chuyển đổi giữa form đơn giản và biến thể
            const select = document.getElementById("type_product");
            const simpleForm = document.querySelector(".simble");
            const variantForm = document.querySelector(".varriant");

            function toggleForms() {
                if (select.value === "0") {
                    simpleForm.style.display = "block";
                    variantForm.style.display = "none";
                } else {
                    simpleForm.style.display = "none";
                    variantForm.style.display = "block";
                }
            }

            select.addEventListener("change", toggleForms);
            toggleForms();

            // Lưu trữ thuộc tính và giá trị đã chọn
            let selectedAttributes = [];
            let selectedAttributeValues = {};

            // Xử lý lưu thuộc tính
            document.getElementById('saveAttributes').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.attribute-checkbox:checked');
                const selectedList = document.getElementById('selectedAttributes');
                const hiddenInput = document.getElementById('selected_attribute_ids');

                selectedList.innerHTML = ''; // Xóa các nút cũ
                selectedAttributes = []; // Reset danh sách thuộc tính
                let selectedIds = [];

                checkboxes.forEach(function(checkbox) {
                    const attrName = checkbox.getAttribute('data-name');
                    const attrId = checkbox.value;

                    // Tạo nút thuộc tính
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.classList.add('btn', 'btn-outline-primary', 'btn-sm', 'attribute-btn');
                    btn.textContent = attrName;
                    btn.setAttribute('data-id', attrId);

                    // Xử lý bấm vào nút thuộc tính để mở modal giá trị
                    btn.addEventListener('click', async function() {
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
                            if (!Array.isArray(attributeValues) || attributeValues
                                .length === 0) {
                                html = '<p>Không có giá trị thuộc tính nào.</p>';
                            } else {
                                attributeValues.forEach(function(value) {
                                    if (value.id && value.value) {
                                        html += `
                                    <div class="form-check">
                                        <input class="form-check-input attribute-value-checkbox" type="checkbox"
                                            value="${value.id}" data-name="${value.value}" id="value_${value.id}">
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
                });

                hiddenInput.value = selectedIds.join(',');
                // Không gọi generateVariants() ở đây
            });

            // Xử lý lưu giá trị thuộc tính
            document.getElementById('saveAttributeValues').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.attribute-value-checkbox:checked');
                const selectedValuesList = document.getElementById('selectedAttributesValues');
                const hiddenValuesInput = document.getElementById('selected_attribute_values_ids');
                let selectedValueIds = hiddenValuesInput.value ? hiddenValuesInput.value.split(',') : [];

                const activeAttributeBtn = document.querySelector('.attribute-btn.active');
                const attributeId = activeAttributeBtn ? activeAttributeBtn.getAttribute('data-id') : null;

                // Xóa các nút giá trị cũ của thuộc tính này
                const existingValueButtons = selectedValuesList.querySelectorAll(
                    `button[data-attribute-id="${attributeId}"]`);
                existingValueButtons.forEach(btn => btn.remove());

                // Lưu giá trị thuộc tính
                selectedAttributeValues[attributeId] = [];
                checkboxes.forEach(function(checkbox) {
                    const valueName = checkbox.getAttribute('data-name');
                    const valueId = checkbox.value;

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.classList.add('btn', 'btn-outline-secondary', 'btn-sm');
                    btn.textContent = valueName;
                    btn.setAttribute('data-id', valueId);
                    btn.setAttribute('data-attribute-id', attributeId);

                    selectedValuesList.appendChild(btn);
                    if (!selectedValueIds.includes(valueId)) {
                        selectedValueIds.push(valueId);
                    }
                    selectedAttributeValues[attributeId].push({
                        id: valueId,
                        value: valueName
                    });
                });

                hiddenValuesInput.value = selectedValueIds.join(',');
                const modal = bootstrap.Modal.getInstance(document.getElementById('attributeValueModal'));
                modal.hide();
                // Không gọi generateVariants() ở đây
            });

            // Thêm class active khi bấm vào nút thuộc tính
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('attribute-btn')) {
                    document.querySelectorAll('.attribute-btn').forEach(btn => btn.classList.remove(
                        'active'));
                    e.target.classList.add('active');
                }
            });

            // Xử lý nút Tạo biến thể
            document.getElementById('generateVariantsBtn').addEventListener('click', generateVariants);

            // Hàm tạo tổ hợp biến thể
            function generateVariants() {
                const variantList = document.getElementById('variantList');
                variantList.innerHTML = ''; // Xóa danh sách biến thể cũ

                // Lấy tất cả giá trị thuộc tính đã chọn
                const attributeValues = Object.values(selectedAttributeValues).filter(values => values.length > 0);
                if (attributeValues.length === 0) {
                    variantList.innerHTML = '<p class="text-danger">Vui lòng chọn thuộc tính và giá trị thuộc tính để tạo biến thể .</p>';
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
                        <th>Giá khuyến mãi</th>
                        <th>Số lượng</th>

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
                        <input type="text" name="variants[${index}][sku]" class="form-control" placeholder="Mã sản phẩm" required>
                        <input type="hidden" name="variants[${index}][attribute_value_ids]" value="${valueIds}">
                    </td>
                    <td>
                        <input type="number" name="variants[${index}][price]" class="form-control" placeholder="Giá" required>

                    </td>
                      <td>
                        <input type="number" name="variants[${index}][price_sale]" class="form-control" placeholder="Giá Khuyến mãi">

                    </td>
                    <td>
                        <input type="number" name="variants[${index}][stock]" class="form-control" placeholder="Số lượng" required>
                    </td>
                    <td>
                        <input type="file" id="thumbnailInput" name="variants[${index}][image]" accept="image/*">
                    </td>
                </tr>
            `;
                });

                html += '</tbody></table>';
                variantList.innerHTML = html;
            }

            // Hàm tạo tổ hợp từ danh sách giá trị thuộc tính
            function generateCombinations(arrays) {
                if (arrays.length === 0) return [];
                return arrays.reduce((acc, curr) => {
                    const result = [];
                    acc.forEach(a => {
                        curr.forEach(c => {
                            result.push([...(Array.isArray(a) ? a : [a]), c]);
                        });
                    });
                    return result;
                }, [
                    []
                ]);
            }
        });
    </script>
@endsection
@section('content')
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-xxl">
                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Thêm mới Sản Phẩm</h4>
                    </div>
                </div>
                <div class="row">
                    @include('component.alert')
                    <div class="col-12">
                        <div class="card">

                            <div class="card-body" id="cardSimple">
                                <div class="container py-4">
                                    <form action="{{ route('admin.products.simple.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-sm-12 row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="name_cate" class="form-label">Tên sản phẩm</label>
                                                    <input type="text" name="name" id="name_cate"
                                                        class="form-control @error('name_cate') is-invalid @enderror"
                                                        value="{{ old('name') }}">
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="decription_short" class="form-label">Mô tả ngắn</label>
                                                    <input type="text" name="decription_short" id="decription_short"
                                                        class="form-control @error('decription_short') is-invalid @enderror"
                                                        value="{{ old('decription_short') }}">
                                                    @error('decription_short')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Mô tả dài</label>
                                                    <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                                                    @error('decription')
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
                                                <div class="mt-3">
                                                    <label>Ảnh sản phẩm chính:</label><br>
                                                    <input type="file" name="thumbnail" id="thumbnailInput"
                                                        accept="image/*"><br>
                                                    <div id="thumbnailPreviewWrapper" style="margin-top: 10px;"></div>

                                                    <label>Album hình ảnh:</label><br>
                                                    <input type="file" name="images[]" id="imageInput" accept="image/*"
                                                        multiple><br>
                                                    <div id="imagePreviewContainer"
                                                        style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;">
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3">
                                                <label for="type_product" class="form-label">Loại sản phẩm</label>
                                                <select name="type_product" id="type_product" class="form-select">
                                                    <option value="0">Sản phẩm đơn giản</option>
                                                    <option value="1">Sản phẩm có biến thể</option>
                                                </select>
                                            </div>

                                            <!-- Form sản phẩm đơn giản -->
                                            <div class="simble">
                                                <div class="mb-3">
                                                    <label for="price" class="form-label">Giá sản phẩm</label>
                                                    <input type="number" name="price" id="price"
                                                        class="form-control @error('price') is-invalid @enderror"
                                                        value="{{ old('price') }}">
                                                    @error('price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="price_sale" class="form-label">Giá khuyến mãi</label>
                                                    <input type="number" name="price_sale" id="name_cate"
                                                        class="form-control @error('price_sale') is-invalid @enderror"
                                                        value="{{ old('price_sale') }}">
                                                    @error('price_sale')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="stook" class="form-label">Số lượng sản phẩm</label>
                                                    <input type="number" name="stook" id="stook"
                                                        class="form-control @error('stook') is-invalid @enderror"
                                                        value="{{ old('stook') }}">
                                                    @error('stook')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>


                                            <!-- Nút chọn thuộc tính -->
                                            <div class="varriant mb-3" style="display: none;">
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    data-bs-toggle="modal" data-bs-target="#attributeModal">
                                                    Chọn thuộc tính
                                                </button>
                                                <div class="mt-3">
                                                    <strong>Chọn giá trị thuộc tính :</strong>
                                                    <div id="selectedAttributes" class="d-flex gap-2 flex-wrap mt-2">
                                                        <!-- Nút thuộc tính sẽ được tạo động ở đây -->
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <strong>Các giá trị thuộc tính đã chọn:</strong>
                                                    <div id="selectedAttributesValues"
                                                        class="d-flex gap-2 flex-wrap mt-2">
                                                        <!-- Nút giá trị thuộc tính sẽ được tạo động ở đây -->
                                                    </div>
                                                </div>
                                                <!-- Nút tạo biến thể -->
                                                <div class="mt-3">
                                                    <button type="button" class="btn btn-sm btn-success"
                                                        id="generateVariantsBtn">Tạo biến thể</button>
                                                </div>
                                                <!-- Khu vực hiển thị biến thể -->
                                                <div class="mt-3">
                                                    <strong>Danh sách biến thể sản phẩm:</strong>
                                                    <div id="variantList" class="mt-2">
                                                        <!-- Danh sách biến thể sẽ được render động ở đây -->
                                                    </div>
                                                </div>
                                            </div>





                                        </div>



                                        <button type="submit" class="btn btn-success">Thêm sản phảm</button>
                                        <a href="{{ route('admin.categories.list-cate') }}"
                                            class="btn btn-secondary">Quay
                                            lại</a>
                                    </form>

                                    <!-- Modal chọn thuộc tính -->
                                    <div class="modal fade" id="attributeModal" tabindex="-1"
                                        aria-labelledby="attributeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Chọn thuộc tính</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Đóng"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="attributeForm">
                                                        @foreach ($attributes as $attribute)
                                                            <div class="form-check">
                                                                <input class="form-check-input attribute-checkbox"
                                                                    type="checkbox" value="{{ $attribute->id }}"
                                                                    id="attribute_{{ $attribute->id }}"
                                                                    data-name="{{ $attribute->name }}">
                                                                <label class="form-check-label"
                                                                    for="attribute_{{ $attribute->id }}">
                                                                    {{ $attribute->name }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </form>
                                                </div>
                                                <div class="modal-footer">

                                                    <button type="button" class="btn btn-primary" id="saveAttributes"
                                                        data-bs-dismiss="modal">Lưu</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal chọn giá trị thuộc tính -->
                                    <!-- Modal chọn giá trị thuộc tính -->
                                    <div class="modal fade" id="attributeValueModal" tabindex="-1"
                                        aria-labelledby="attributeValueModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="attributeValueModalLabel">Chọn giá trị
                                                        thuộc tính</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Đóng"></button>
                                                </div>
                                                <div class="modal-body" id="attributeValuesContent">
                                                    <!-- Danh sách giá trị thuộc tính sẽ được thêm động vào đây -->
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Đóng</button>
                                                    <button type="button" class="btn btn-primary"
                                                        id="saveAttributeValues">Lưu</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Input ẩn để lưu ID giá trị thuộc tính -->
                                    <input type="hidden" id="selected_attribute_values_ids"
                                        name="selected_attribute_values_ids">

                                    <!-- Input ẩn để lưu ID giá trị thuộc tính -->
                                    <input type="hidden" id="selected_attribute_values_ids"
                                        name="selected_attribute_values_ids">
                                </div>
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
