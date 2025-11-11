<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Admin\Category;
use App\Models\Admin\Product;
use App\Models\Admin\ProductAttribute;
use App\Models\Admin\ProductAttributeValue;
use App\Models\Admin\ProductImage;
use App\Models\Admin\ProductVariant;
use App\Models\Admin\ProductVariantValue;
use Attribute;
use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class ProductController extends Controller
{
    public function home()
    {
        return view('admin.products.home');
    }
    public function index()
    {

        $products = Product::orderBy('created_at', 'desc')->with('category')->paginate(10);
        return view('admin.products.list', compact('products'));
    }

    public function createSimple()
    {
        $attributes = ProductAttribute::all();
        $categories = Category::all();
        return view('admin.products.addSimple', compact('categories', 'attributes'));
    }
    public function create()
    {
        $attributes = ProductAttribute::all();
        $categories = Category::all();
        return view('admin.products.add', compact('categories', 'attributes'));
    }


    public function checkSKU($sku)
    {
        $exists = ProductVariant::where('sku', $sku)->exists();
        return response()->json(['isUnique' => !$exists]);
    }

    public function store(Request $request)
    {
        // Khởi tạo các mảng/biến để lưu đường dẫn tạm thời
        $tempThumbnailPath = null;
        $tempImagesPathArray = [];

        // --- 1. Thực hiện Validation ---
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255',
            'price'            => 'required|numeric|min:0',
            'price_sale'       => 'nullable|numeric|lt:price',
            'stook'            => 'required|integer|min:1',
            'decription_short' => 'nullable|string|max:500',
            'description'      => 'nullable|string',

            'status'           => ['required', Rule::in(['0', '1'])],
            'category_id'      => 'required|integer',

            // Thumbnail: bắt buộc nếu không có ảnh cũ
            'thumbnail'        => [
                Rule::requiredIf(!$request->input('old_thumbnail_path')),
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048'
            ],

            // Album ảnh
            'images.*'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            // Tên sản phẩm
            'name.required' => 'Tên sản phẩm không được để trống.',
            'name.max'      => 'Tên sản phẩm tối đa 255 ký tự.',

            // Giá
            'price.required' => 'Giá không được để trống.',
            'price.numeric'  => 'Giá phải là số.',
            'price.min'      => 'Giá phải lớn hơn hoặc bằng 0.',

            // Giá sale
            'price_sale.numeric' => 'Giá khuyến mãi phải là số.',
            'price_sale.lt'      => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',

            // Tồn kho
            'stook.required' => 'Số lượng tồn kho không được để trống.',
            'stook.integer'  => 'Số lượng phải là số nguyên.',
            'stook.min'      => 'Số lượng phải lớn hơn hoặc bằng 1.',

            // Mô tả ngắn
            'decription_short.max' => 'Mô tả ngắn tối đa 500 ký tự.',

            // Trạng thái
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in'       => 'Giá trị trạng thái không hợp lệ.',

            // Danh mục
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.integer'  => 'Danh mục không hợp lệ.',

            // Thumbnail
            'thumbnail.required' => 'Vui lòng chọn ảnh thumbnail.',
            'thumbnail.image'    => 'Thumbnail phải là hình ảnh.',
            'thumbnail.mimes'    => 'Ảnh phải có định dạng jpeg, png, jpg, gif, svg.',
            'thumbnail.max'      => 'Ảnh thumbnail không vượt quá 2MB.',

            // Album
            'images.*.image' => 'Mỗi file trong album phải là hình ảnh.',
            'images.*.mimes' => 'Ảnh trong album phải có định dạng jpeg, png, jpg, gif, svg.',
            'images.*.max'   => 'Mỗi ảnh trong album không vượt quá 2MB.',
        ]);

        // --- 2. XỬ LÝ LƯU FILE TẠM THỜI NẾU VALIDATION THẤT BẠI ---
        $tempThumbnailPath = $request->input('old_thumbnail_path', ''); // Mặc định là path cũ
        $tempDir = 'temp_uploads/products';
        $disk = 'public';

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');

            // LƯU FILE VÀO THƯ MỤC storage/app/public

            $path = $file->store($tempDir, $disk);

            // CHUYỂN PATH THÀNH URL để hiển thị (hoặc dùng Base64 như giải pháp trước)
            $tempThumbnailPath = Storage::disk($disk)->url($path);
        }

        // Tương tự cho Album ảnh...
        $tempImagesPathArray = json_decode($request->input('old_images_path') ?? '[]', true);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $path = $file->store($tempDir, $disk);
                    $tempImagesPathArray[] = Storage::disk($disk)->url($path);
                }
            }
        }


        // 3. Nếu validation thất bại, redirect và flash data (bao gồm path mới lưu)
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)
                ->withInput()
                ->with('thumbnail_path', $tempThumbnailPath) // FLASH PATH/URL MỚI ĐÃ LƯU
                ->with('images_path', $tempImagesPathArray);
        }

        // Chuẩn bị dữ liệu giá: loại bỏ dấu cách và dấu chấm
        if (!empty($request->price)) {
            $cleanPrice = str_replace([' ', '.'], '', $request->price);
        } else {
            $cleanPrice = null;
        }
        if (!empty($request->price_sale)) {
            $cleanPriceSale = str_replace([' ', '.'], '', $request->price_sale);
        } else {
            $cleanPriceSale = null;
        }
        $type_pro = request()->input('type_product');
        if ($type_pro == 0) {

            // Tạo thư mục lưu ảnh nếu chưa có
            if (!Storage::exists('public/products')) {
                Storage::makeDirectory('public/products');
            }

            // Xử lý ảnh thumbnail
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                $thumbnail = $request->file('thumbnail');
                $thumbnailName = Str::slug(pathinfo($thumbnail->getClientOriginalName(), PATHINFO_FILENAME))
                    . '_' . time() . '_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();

                // Lưu file vào storage/app/public/products
                $thumbnail->storeAs('products', $thumbnailName, 'public');

                // Lưu đường dẫn không có 'public/' để dùng Storage::url()
                $thumbnailPath = 'products/' . $thumbnailName;
            }

            // Tạo sản phẩm mới với dữ liệu đã chuẩn bị
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'is_variant' => '0',
                'status' => $request->status,
                'price' => $cleanPrice,
                'stook' => $request->stook,
                'price_sale' => $cleanPriceSale,
                'decription_short' => $request->decription_short,
                'thumbnail' => $thumbnailPath,
            ]);

            // Xử lý nhiều ảnh chi tiết
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $imageFile) {
                    if ($imageFile->isValid()) {
                        $imageName = Str::slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME))
                            . '_' . time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();

                        // Lưu file vào storage/app/public/products

                        $imageFile->storeAs('products', $imageName, 'public');

                        // Lưu đường dẫn ảnh chi tiết vào DB
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image' => 'products/' . $imageName,
                        ]);
                    }
                }
            }
        } elseif ($type_pro == 1) {

            // Tạo thư mục lưu ảnh nếu chưa có
            if (!Storage::exists('public/products')) {
                Storage::makeDirectory('public/products');
            }

            // Xử lý ảnh thumbnail
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                $thumbnail = $request->file('thumbnail');
                $thumbnailName = Str::slug(pathinfo($thumbnail->getClientOriginalName(), PATHINFO_FILENAME))
                    . '_' . time() . '_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();

                // Lưu file vào storage/app/public/products
                $thumbnail->storeAs('products', $thumbnailName, 'public');

                // Lưu đường dẫn không có 'public/' để dùng Storage::url()
                $thumbnailPath = 'products/' . $thumbnailName;
            }
            //thêm product
            $data = [
                'name' => request()->input('name'),
                'decription_short' => request()->input('decription_short'),
                'description' => request()->input('description'),
                'status' => request()->input('status'),
                'category_id' => request()->input('category_id'),
                'is_variant' => '1',
                'thumbnail' => $thumbnailPath,
            ];
            $product = Product::create($data);
            // Xử lý nhiều ảnh chi tiết
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $imageFile) {
                    if ($imageFile->isValid()) {
                        $imageName = Str::slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME))
                            . '_' . time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();

                        // Lưu file vào storage/app/public/products

                        $imageFile->storeAs('products', $imageName, 'public');

                        // Lưu đường dẫn ảnh chi tiết vào DB
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image' => 'products/' . $imageName,
                        ]);
                    }
                }
            }

            foreach ($request->variants as $index => $variant) {

                $imagePath = null;
                if ($request->hasFile("variants.{$index}.image")) {
                    $imagePath = $request->file("variants.{$index}.image")->store('variant_images', 'public');
                }

                //thêm biến thểthể
                $valueIds = explode(',', $variant['attribute_value_ids']);

                $variant = [
                    'sku' => $variant['sku'],
                    'product_id' => $product->id,
                    'price' => $variant['price'],
                    'sale_price' => $variant['price_sale'],
                    'quantity' => $variant['stock'],
                    'image' => $imagePath,
                ];
                $productvariant = ProductVariant::create($variant);
                //thêm giá trị thuộc tính



                foreach ($valueIds as $valueId) {
                    $valueAttribute = ProductAttributeValue::findOrFail($valueId);


                    ProductVariantValue::create([
                        'variant_id' => $productvariant->id,
                        'attribute_value_id' => $valueId,
                        'value' => $valueAttribute->value
                    ]);
                }
            }
        } else {
            return redirect()->route('admin.products.list')->with('error', 'Thêm mới sản phẩm thất bại!');
        }


        // Chuyển hướng về danh sách sản phẩm với thông báo thành công
        return redirect()->route('admin.products.list')->with('success', 'Thêm mới sản phẩm thành công!');
    }


    public function show($id)
    {
        $product = Product::with([
            'images',
            'category',
            'variants.values' => function ($query) {
                $query->with(['attributeValue' => function ($q) {
                    $q->select('id', 'value');
                }]);
            }
        ])->findOrFail($id);
        return view('admin.products.simple.detail', compact('product'));
    }
    public function edit() {}
    public function update() {}


    public function delete(Request $request)
    {
        $id = $request->input('id');

        // Tìm sản phẩm
        $product = Product::find($id);

        if (!$product) {
            return redirect()->route('admin.products.list')->with('error', 'Sản phẩm không tồn tại.');
        }

        // Xóa ảnh đại diện (thumbnail) nếu có
        if ($product->thumbnail && Storage::disk('public')->exists($product->thumbnail)) {
            Storage::disk('public')->delete($product->thumbnail);
        }

        // Lấy và xóa ảnh chi tiết nếu có
        $images = ProductImage::where('product_id', $id)->get();
        foreach ($images as $image) {

            if ($image->image && Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }
        }

        // Xóa bản ghi ảnh khỏi DB
        ProductImage::where('product_id', $id)->delete();

        // Xóa sản phẩm
        $product->delete();

        return redirect()->route('admin.products.simple.list')->with('success', 'Xóa sản phẩm thành công.');
    }

    public function test()
    {
        return view('admin.products.variants.homevariant');
    }

    public function createPost(Request $request)
    {


        // --- 3. VALIDATION THÀNH CÔNG ---

        // Nếu validation thành công, dừng chương trình và in dữ liệu
        return dd($request->all());
    }
}
