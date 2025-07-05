<?php

use App\Models\Admin\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/attributevalues/{id}', [App\Http\Controllers\Api\ProductAttributeValueueController::class, 'getByAttributeId']);

Route::get('/get-variant-info', [App\Http\Controllers\Api\ProductClientController::class, 'getVariantInfo']);
