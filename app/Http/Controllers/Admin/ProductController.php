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


class ProductController extends Controller
{
    public function home()
    {
        return view('admin.products.home');
    }
    public function index()
    {

        $products = Product::orderBy('created_at', 'desc')->with('category')->paginate(10);
        return view('admin.products.simple.list', compact('products'));
    }

    public function create()
    {
        $attributes = ProductAttribute::all();
        $categories = Category::all();
        return view('admin.products.simple.add', compact('categories', 'attributes'));
    }


    public function checkSKU($sku)
    {
        $exists = ProductVariant::where('sku', $sku)->exists();
        return response()->json(['isUnique' => !$exists]);
    }

    public function store(Request $request)
    {

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
                        $valueAttribute =ProductAttributeValue::findOrFail($valueId);


                            ProductVariantValue::create([
                                'variant_id' => $productvariant->id,
                                'attribute_value_id' => $valueId,
                                'value'=>$valueAttribute->value
                            ]);


            }}
        } else {
            return redirect()->route('admin.products.simple.list')->with('error', 'Thêm mới sản phẩm thất bại!');
        }


        // Chuyển hướng về danh sách sản phẩm với thông báo thành công
        return redirect()->route('admin.products.simple.list')->with('success', 'Thêm mới sản phẩm thành công!');
    }


    public function show($id) {
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
}
