 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

 <div class="container py-4">
     <h2 class="mb-4">Thêm giá trị thuộc tính</h2>

     <form action="{{ route('admin.products.variants.attributes.value.add') }}" method="POST">
         @csrf

         <div class="mb-3">
             <label for="value" class="form-label">Tên giá trị</label>
             <input type="text" name="value" id="value" class="form-control @error('value') is-invalid @enderror"
                 value="{{ old('value') }}">
             @error('value')
                 <div class="invalid-feedback">{{ $message }}</div>
             @enderror
             <input type="hidden" name="attribute_id" value="{{$id}}">
         </div>




 <button type="submit" class="btn btn-success">Lưu giá trị</button>

 </form>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
 </div>

