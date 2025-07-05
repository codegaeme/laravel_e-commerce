<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\ProductAttributeValue;
use Illuminate\Http\Request;

class ProductAttributeValueueController extends Controller
{
   public function getByAttributeId(Request $request ,$id)
    {
        $attributeId = $id;
        $values = ProductAttributeValue::where('attribute_id', $attributeId)->get(['id', 'value']);
    
        return response()->json($values);
    }
}
