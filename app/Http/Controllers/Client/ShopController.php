<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Admin\Category;
use App\Models\Admin\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
 public function shop(Request $request)
    {

        $query = Product::query()->with('category');

        $categories = Category::where('status', 1)->get();


        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }


        if ($request->has('price_range') && $request->price_range != '') {

            list($min, $max) = explode('-', $request->price_range);
            $query->whereBetween('price', [(int)$min, (int)$max]);
        }


        $products = $query->latest()->paginate(12);


        return view('client.shop.shop', compact('products', 'categories'));
    }
}
