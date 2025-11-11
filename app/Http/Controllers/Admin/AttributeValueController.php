<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\ProductAttribute;
use App\Models\Admin\ProductAttributeValue;
use App\Models\Attribute as ModelsAttribute;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;


class AttributeValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $id = $request->id;
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
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $title = "Thêm mới giá trị thuộc tính";
        $att = ProductAttribute::findOrFail($id);
        return view('admin.attributevalue.add', compact('att', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate(
            [
                'name' => 'required|string|max:255|unique:product_attribute_values,value'
            ],
            [
                ''
            ]
        );

        $data = [
            'value' => $request->name,
            'attribute_id' => $request->id_att,
        ];

        ProductAttributeValue::create($data);
        return redirect()->route('admin.attributes.index')->with('success', 'Thêm giá trị thuộc tính thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, $att_id)
    {
        $title = "Cập nhật giá trị thuộc tính";
        $id_att = $att_id;
        $att = ProductAttributeValue::findOrFail($id);
        return view('admin.attributevalue.edit', compact('att', 'id_att', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_attribute_values,value'
        ]);
        $att = ProductAttributeValue::findOrFail($id);
        $att->update([
            'value' => $request->name,
            'attribute_id' => $request->id_att,

        ]);
        return redirect()->route('admin.attributes.show', $request->id_att)->with('success', 'Sửa thuộc tính thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $att = ProductAttributeValue::findOrFail($id);
        $att->delete();
        return redirect()->route('admin.attributes.show', $request->id_att)->with('success', 'Xóa thuộc tính thành công!');
    }
}
