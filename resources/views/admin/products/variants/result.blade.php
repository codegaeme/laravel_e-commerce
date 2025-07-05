<h2>Tất cả các biến thể có thể tạo</h2>

@if (count($variants))
    <table class="table table-bordered">
        <thead>
            <tr>
                @foreach ($attributes as $attr)
                    <th>{{ $attr->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($variants as $variant)
                <tr>
                    @foreach ($variant as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-muted">Không có biến thể nào được tạo vì thiếu giá trị thuộc tính.</p>
@endif

<a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Quay lại</a>
