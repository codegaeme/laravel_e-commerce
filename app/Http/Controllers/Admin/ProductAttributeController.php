<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\ProductAttribute;
use App\Models\Admin\ProductAttributeValue;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $title = "Danh sách thuộc tinh";
        $listAtt = ProductAttribute::query()->when($keyword, function ($q) use ($keyword) {
            $q->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            });
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());;

        return view('admin.attribute.list', compact('listAtt', 'title'));
    }
    public function create()
    {
        $title = "Thêm mới thuộc tính";
        return view('admin.attribute.add', compact('title'));
    }
    public function store(Request $request)
    {
        $request->validate(
            [
                'name'  => 'required|string|min:1|max:255|unique:product_attributes,name'
            ],
            [
                'name.required' => 'Tên  thuộc tính không được để trống',
                'name.unique'   => 'Tên thuộc tính đã tồn tại',
                'name.max'      => 'Tên  thuộc tính tối đa 255 ký tự',
            ]
        );

        $data = [
            'name' => request()->input('name'),
        ];
        $newAttribute = ProductAttribute::create($data);
        if ($newAttribute) {
            return redirect()->route('admin.attributes.index')->with('success', 'Thêm thành công');
        } else {
            return redirect()->route('admin.attributes.index')->with('error', 'Thêm thất bại');
        }
    }
    public function show(string $id, Request $request)
    {
        $keyword = $request->keyword;
        $title = "Danh sách giá trị thuộc tính";
        $att = ProductAttribute::findOrFail($id);

        $listAttValue = ProductAttributeValue::where('attribute_id', $att->id)
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('value', 'like', "%{$keyword}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.attribute.show', compact('att', 'title', 'listAttValue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = "Sửa thuộc tính";
        $att = ProductAttribute::findOrFail($id);

        return view('admin.attribute.edit', compact('att', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(
            [
                'name'  => 'required|string|min:1|max:255|unique:product_attributes,name,' . $id,
            ],
            [
                'name.required' => 'Tên thuộc tính không được để trống',
                'name.unique'   => 'Tên thuộc tính đã tồn tại',
                'name.max'      => 'Tên thuộc tính tối đa 255 ký tự',
            ]
        );

        $att = ProductAttribute::findOrFail($id);
        $att->update([
            'name' => $request->name,


        ]);
        return redirect()->route('admin.attributes.index')->with('success', 'Sửa thuộc tính thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $att = ProductAttribute::findOrFail($id);
        $att->delete();
        return redirect()->route('admin.attributes.index')->with('success', 'Xóa thuộc tính thành công!');
    }
}
