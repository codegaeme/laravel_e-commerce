<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\ProductAttribute;
use Illuminate\Http\Request;

class VariantController extends Controller
{
public function generateVariants()
{
    // Lấy toàn bộ thuộc tính có value
    $attributes = ProductAttribute::with('values')->get();

    // Bỏ thuộc tính không có giá trị
    $attributes = $attributes->filter(function ($attr) {
        return $attr->values && $attr->values->count() > 0;
    });

    // Lấy danh sách giá trị cho từng thuộc tính
    $attributeValues = [];
    foreach ($attributes as $attribute) {
        $attributeValues[] = $attribute->values->pluck('value')->toArray();
    }

    // Nếu có ít nhất 1 nhóm giá trị thì sinh tổ hợp
    if (count($attributeValues) > 0) {
        $variants = $this->cartesianProduct($attributeValues);
    } else {
        $variants = [];
    }

    return view('admin.products.variants.result', compact('variants', 'attributes'));
}

// Hàm sinh tổ hợp (Cartesian Product)
private function cartesianProduct($arrays)
{
    $result = [[]];
    foreach ($arrays as $values) {
        $append = [];
        foreach ($result as $product) {
            foreach ($values as $item) {
                $append[] = array_merge($product, [$item]);
            }
        }
        $result = $append;
    }
    return $result;
}

}
