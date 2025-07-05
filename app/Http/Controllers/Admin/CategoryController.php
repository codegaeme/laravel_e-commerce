<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CateGoryRequest;
use App\Http\Requests\Admin\CateUpdateRequest;
use App\Models\Admin\Category;
use App\Models\Admin\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.categories.list', compact('categories'));
    }
    public function create()
    {
        return view('admin.categories.add');
    }
    public function store(CateGoryRequest $request)
    {
        $request->validated();
        $data = [
            'name_cate' => $request->name_cate,
            'description' => $request->description,
            'status' => $request->status,

        ];
        $newCate = Category::create($data);
        return redirect()->route('admin.categories.list-cate')->with('success', 'Thêm mới thành công');
    }
    public function show()
    {
        return 'list';
    }

    public function edit($id)
    {
        $category = Category::find($id);
        if ($category) {
            return view('admin.categories.edit', compact('category'));
        } else {
            return redirect()->back()->with('error', 'Không tồn tại');
        }
    }
    public function update($id, CateUpdateRequest $request)
    {
        $request->validate([
            'name_cate' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name_cate')->ignore($id),
            ],
        ], [
            'name_cate.required' => 'Vui lòng nhập tên danh mục.',
            'name_cate.unique'   => 'Tên danh mục đã tồn tại.',
            'name_cate.max'      => 'Tên danh mục không được vượt quá 255 ký tự.',
        ]);
        $category = Category::find($id);
        if ($category) {
            $data = [
                'name_cate' => $request->name_cate,
                'description' => $request->description,
                'status' => $request->status,

            ];
            $category->update($data);
            return redirect()->route('admin.categories.list-cate')->with('success', 'Sửa thành công');
        } else {
            return redirect()->back()->with('error', 'Không tồn tại');
        }
    }
    public function delete(Request $request)
    {
        $id = $request->id;
        $category = Category::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Danh mục không tồn tại.');
        }

        // Kiểm tra xem còn sản phẩm nào đang dùng danh mục này không
        $productCount = Product::where('category_id', $id)->count();

        if ($productCount > 0) {
            return redirect()->back()->with('warning', 'Danh mục này đang có sản phẩm, không thể xóa.');
        }


        $category->delete();

        return redirect()->back()->with('success', 'Xóa danh mục thành công.');
    }
}
