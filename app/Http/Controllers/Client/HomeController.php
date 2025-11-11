<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Admin\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(){
    $products= Product::with('category','images','variants')->latest()
            ->take(10)
            ->get();;

        return view('component.client.home',compact('products'));
    }
}
