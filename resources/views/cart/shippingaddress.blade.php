@extends('component.client.layout.masterlayoutsclient')

@section('title')
    Thêm địa chỉ giao hàng
@endsection

@section('css')
    <style>
        .form-container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .form-container h4 {
            margin-bottom: 25px;
            font-weight: 600;
        }

        .address-preview {
            font-style: italic;
            color: #6c757d;
            font-size: 0.95rem;
            margin-top: 10px;
        }
    </style>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('province');
            const districtSelect = document.getElementById('district');
            const wardSelect = document.getElementById('ward');
            const preview = document.getElementById('full-address-preview');

            const oldProvince = provinceSelect.dataset.old;
            const oldDistrict = districtSelect.dataset.old;
            const oldWard = wardSelect.dataset.old;

            // Load tỉnh
            fetch('https://provinces.open-api.vn/api/p/')
                .then(res => res.json())
                .then(data => {
                    data.forEach(p => {
                        provinceSelect.innerHTML +=
                            `<option value="${p.code}" data-name="${p.name}">${p.name}</option>`;
                    });

                    if (oldProvince) {
                        provinceSelect.value = oldProvince;
                        provinceSelect.dispatchEvent(new Event('change'));
                    }
                });

            // Load huyện khi chọn tỉnh
            provinceSelect.addEventListener('change', function() {
                const provinceCode = this.value;
                districtSelect.innerHTML = '<option value="">-- Chọn huyện --</option>';
                wardSelect.innerHTML = '<option value="">-- Chọn xã --</option>';

                if (provinceCode) {
                    fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
                        .then(res => res.json())
                        .then(data => {
                            data.districts.forEach(d => {
                                districtSelect.innerHTML +=
                                    `<option value="${d.code}" data-name="${d.name}">${d.name}</option>`;
                            });

                            if (oldDistrict) {
                                districtSelect.value = oldDistrict;
                                districtSelect.dispatchEvent(new Event('change'));
                            }
                        });
                }

                updatePreview();
            });

            // Load xã khi chọn huyện
            districtSelect.addEventListener('change', function() {
                const districtCode = this.value;
                wardSelect.innerHTML = '<option value="">-- Chọn xã --</option>';

                if (districtCode) {
                    fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
                        .then(res => res.json())
                        .then(data => {
                            data.wards.forEach(w => {
                                wardSelect.innerHTML +=
                                    `<option value="${w.code}" data-name="${w.name}">${w.name}</option>`;
                            });

                            if (oldWard) {
                                wardSelect.value = oldWard;
                            }

                            updatePreview();
                        });
                } else {
                    updatePreview();
                }
            });

            wardSelect.addEventListener('change', updatePreview);
            document.getElementById('address_detail').addEventListener('input', updatePreview);

            function updatePreview() {
                const detail = document.getElementById('address_detail').value;
                const ward = wardSelect.selectedOptions[0]?.dataset?.name || '';
                const district = districtSelect.selectedOptions[0]?.dataset?.name || '';
                const province = provinceSelect.selectedOptions[0]?.dataset?.name || '';
                const parts = [detail, ward, district, province].filter(Boolean);
                preview.textContent = parts.length ? `Địa chỉ đầy đủ: ${parts.join(', ')}` : '';
                document.getElementById('full_address').value = parts;
            }
        });
    </script>
@endsection

@section('content')
    <div class="container mt-5">
        <div class="form-container">
            <h4>Thêm địa chỉ giao hàng</h4>

            <form action="{{ route('shippingAdddressPost') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Họ và tên người nhận</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                        name="phone" value="{{ old('phone') }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="province">Tỉnh / Thành phố</label>
                    <select class="form-control @error('province') is-invalid @enderror" id="province" name="province"
                        data-old="{{ old('province') }}">
                        <option value="">-- Chọn tỉnh --</option>
                    </select>
                    @error('province')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="district">Quận / Huyện</label>
                    <select class="form-control @error('district') is-invalid @enderror" id="district" name="district"
                        data-old="{{ old('district') }}">
                        <option value="">-- Chọn huyện --</option>
                    </select>
                    @error('district')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="ward">Phường / Xã</label>
                    <select class="form-control @error('ward') is-invalid @enderror" id="ward" name="ward"
                        data-old="{{ old('ward') }}">
                        <option value="">-- Chọn xã --</option>
                    </select>
                    @error('ward')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="address_detail" class="form-label">Địa chỉ chi tiết</label>
                    <textarea class="form-control @error('address_detail') is-invalid @enderror" id="address_detail" name="address_detail"
                        rows="3" placeholder="Số nhà, tên đường...">{{ old('address_detail') }}</textarea>
                    @error('address_detail')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="full-address-preview" class="address-preview"></div>
                </div>
                <input type="text" name="address" id="full_address">

                <button type="submit" class="btn btn-primary w-100">Lưu địa chỉ</button>
            </form>
        </div>
    </div>
@endsection
