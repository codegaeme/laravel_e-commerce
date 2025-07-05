<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\ProductAttribute;
use App\Models\Admin\ProductAttributeValue;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
        public function index(){


          $data = ProductAttribute::with(['values'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('admin.products.variants.attribute.list',compact('data'));
    }
    public function create(){
        return view('admin.products.variants.attribute.add');
    }
      public function store(Request $request){
        $data = [
            'name'=> request()->input('name'),
        ];
        $newAttribute = ProductAttribute::create($data);
        if ($newAttribute) {
             return redirect()->route('admin.products.variants.attributes.index')->with('success','Thêm thành công');
        }
        else{
              return redirect()->route('admin.products.variants.attributes.index')->with('error','Thêm thất bại');
        }

    }
    public function addValue($id){
        return view('admin.products.variants.attribute.detail',compact('id'));
    }
    public function add(Request $request ){

         $data = [
            'value'=> request()->input('value'),
            'attribute_id'=>request()->input('attribute_id')
        ];
        $newAttribute = ProductAttributeValue::create($data);
        if ($newAttribute) {
             return redirect()->route('admin.products.variants.attributes.index')->with('success','Thêm thành công');
        }
        else{
              return redirect()->route('admin.products.variants.attributes.index')->with('error','Thêm thất bại');
        }
    }
    public function delete(Request $request){
        $id = $request->id;
        $category = ProductAttribute::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Thuộc tính này không tồn tại.');
        }

        // Kiểm tra xem còn sản phẩm nào đang dùng danh mục này không
        $productCount = ProductAttributeValue::where('attribute_id',$id)->count();

        if ($productCount > 0) {
            return redirect()->back()->with('warning', 'Thuộc tính này đang có giá trị, không thể xóa.');
        }


        $category->delete();

        return redirect()->back()->with('success', 'Xóa thuộc tính thành công.');
    }
    public function edit($id){
        $att=ProductAttribute::find($id);
         if (!$att) {
            return redirect()->back()->with('error', 'Thuộc tính này không tồn tại.');
        }
        return view('admin.products.variants.attribute.edit',compact('att'));
    }
      public function update(Request $request, $id){
        //  $request->validate([
        //     'name_cate' => [
        //         'required',
        //         'string',
        //         'max:255',
        //         Rule::unique('categories', 'name_cate')->ignore($id),
        //     ],
        // ], [
        //     'name_cate.required' => 'Vui lòng nhập tên danh mục.',
        //     'name_cate.unique'   => 'Tên danh mục đã tồn tại.',
        //     'name_cate.max'      => 'Tên danh mục không được vượt quá 255 ký tự.',
        // ]);
        $category = ProductAttribute::find($id);
        if ($category) {
            $data = [
                'name' => request()->input('name'),
            ];
          
            $category->update($data);
            return redirect()->route('admin.products.variants.attributes.index')->with('success', 'Sửa thành công');
        } else {
            return redirect()->back()->with('error', 'Không tồn tại');
        }
    }
}
