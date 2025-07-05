<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Admin\Product;
use App\Models\Admin\ProductAttribute;
use App\Models\Admin\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductClientController extends Controller
{
    public function detail($id)
    {
       $product = Product::with([
    'category',
    'images',
    'variants.values.attributeValue.attribute' // đảm bảo quan hệ được đặt đúng trong model
])->findOrFail($id);

$usedAttributeValues = collect();

foreach ($product->variants as $variant) {
    foreach ($variant->values as $value) {
        if ($value->attributeValue) {
            $usedAttributeValues->push($value->attributeValue);
        }
    }
}

// Xoá trùng (theo id của attribute value)
$uniqueAttributeValues = $usedAttributeValues->unique('id');
    //  $atrr =ProductAttribute::with('values')->get();
$atrr = $uniqueAttributeValues->groupBy(function ($item) {
    return $item->attribute->name; // hoặc ->product_attribute_id nếu muốn group theo ID
});


        return view('client.products.detail', compact('product','atrr'));
    }

public function findVariant(Request $request)
{
     $productId = $request->input('product_id');
    $attributes = $request->input('attributes'); // ['Màu sắc' => 1, 'Size' => 3]
    $valueIds = array_values($attributes);

    $variantId = DB::table('product_variant_values as pv')
        ->join('product_variants as v', 'pv.variant_id', '=', 'v.id')
        ->where('v.product_id', $productId)
        ->whereIn('pv.attribute_value_id', $valueIds)
        ->groupBy('pv.variant_id')
        ->havingRaw('COUNT(*) = ?', [count($valueIds)])
        ->value('pv.variant_id');

    if (!$variantId) {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy biến thể phù hợp.'
        ]);
    }

    $variant = ProductVariant::find($variantId);

    return response()->json([
        'success' => true,
        'data' => [
            'id_variant' =>$variant->id,
            'price' => number_format($variant->price, 0, ',', '.'),
            'quantity' => $variant->quantity,
             'image' => $variant->image
            ? asset('storage/' . $variant->image)
            : asset('images/no-image.png'),


        ]


    ]);

}
}

