<?php

use App\Http\Controllers\Admin\VariantController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/my-order-detail/{id}', [App\Http\Controllers\Client\OrderController::class, 'myOrderDetail'])->name('myOrderDetail')->middleware('auth');
Route::get('/my-order', [App\Http\Controllers\Client\OrderController::class, 'myOrder'])->name('myOrder')->middleware('auth');

Route::post('/checkout-post', [App\Http\Controllers\Client\CheckoutController::class, 'checkoutPost'])->name('checkoutPost');

Route::get('/success', [App\Http\Controllers\Client\CheckoutController::class, 'success'])->name('success')->middleware('auth');
Route::post('/shippingaddressPost', [App\Http\Controllers\Client\CheckoutController::class, 'spadPost'])->name('shippingAdddressPost')->middleware('auth');
Route::get('/shippingaddress', [App\Http\Controllers\Client\CheckoutController::class, 'spad'])->name('shippingAdddress');
Route::post('/checkout', [App\Http\Controllers\Client\CheckoutController::class, 'checkout'])->name('checkout');
Route::post('/cart/update', [App\Http\Controllers\Client\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [App\Http\Controllers\Client\CartController::class, 'remove'])->name('cart.remove');

Route::post('addcart', [App\Http\Controllers\Client\CartController::class, 'add'])->name('add-cart');
Route::get('cart', [App\Http\Controllers\Client\CartController::class, 'list'])->name('list-cart');
Route::get('/', [App\Http\Controllers\Client\HomeController::class, 'home'])->name('home');
Route::get('/product/detail/{id}', [App\Http\Controllers\Client\ProductClientController::class, 'detail'])->name('product.detail');
Route::post('/find-variant', [App\Http\Controllers\Client\ProductClientController::class, 'findVariant'])->name('findVariant');


Route::post('/renderVariant', [App\Http\Controllers\Client\ProductClientController::class, 'variant'])->name('renderVarianrt');

Route::prefix('admin')->as('admin.')->group(function () {

    Route::get('/t', [App\Http\Controllers\Admin\ProductController::class, 'test'])->name('test');
    Route::post('/test', [App\Http\Controllers\Admin\ProductController::class, 'createPost'])->name('testpost');
    //danhmuc
    Route::prefix('categories')->as('categories.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('list-cate');
        Route::get('/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('create-cate');
        Route::post('/store', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('store-cate');
        Route::get('/show', [App\Http\Controllers\Admin\CategoryController::class, 'show'])->name('show-cate');
        Route::get('/edit/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('edit-cate');
        Route::put('/update/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('update-cate');
        Route::delete('/delete', [App\Http\Controllers\Admin\CategoryController::class, 'delete'])->name('delete-cate');
    });
    //product
    // simple
    Route::prefix('products')->as('products.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('list');
        Route::get('/createSimple', [App\Http\Controllers\Admin\ProductController::class, 'createSimple'])->name('createSimple');
        Route::get('/create', [App\Http\Controllers\Admin\ProductController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('store');
        Route::get('/show/{id}', [App\Http\Controllers\Admin\ProductController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [App\Http\Controllers\Admin\ProductController::class, 'update'])->name('update');
        Route::delete('/delete', [App\Http\Controllers\Admin\ProductController::class, 'delete'])->name('delete');
    });

    // thuọc tính
    Route::resource('attributes', App\Http\Controllers\Admin\ProductAttributeController::class);

    //giá trị thuộc tính
    Route::prefix('attributeValues')->as('attributeValues.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AttributeValueController::class, 'index'])->name('index');        // Danh sách sản phẩm
        Route::get('/createSimple/{id}', [App\Http\Controllers\Admin\AttributeValueController::class, 'create'])->name('createSimple'); // Form thêm mới
        Route::post('/store', [App\Http\Controllers\Admin\AttributeValueController::class, 'store'])->name('store');   // Xử lý lưu sản phẩm
        Route::get('/edit/{id}/{attribute_id}', [App\Http\Controllers\Admin\AttributeValueController::class, 'edit'])->name('edit');  // Form chỉnh sửa
        Route::put('/update/{id}', [App\Http\Controllers\Admin\AttributeValueController::class, 'update'])->name('update'); // Cập nhật sản phẩm
        Route::delete('/delete/{id}', [App\Http\Controllers\Admin\AttributeValueController::class, 'destroy'])->name('destroy'); // Xóa
    });
    Route::get('/dh', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    //orders

    Route::get('orders', [App\Http\Controllers\Admin\OrderManagerController::class, 'index'])->name('order-list');
    Route::put('update-status-orders', [App\Http\Controllers\Admin\OrderManagerController::class, 'changeStt'])->name('update-status-orders');
    Route::put('update-status-orders-cus', [App\Http\Controllers\Admin\OrderManagerController::class, 'changeSttCus'])->name('update-status-orders-cus');
});
