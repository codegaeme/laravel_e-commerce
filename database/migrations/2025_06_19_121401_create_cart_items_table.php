<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('product_id');
            $table->string('is_variant')->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 2); // giá lúc người dùng thêm vào giỏ

            $table->unsignedBigInteger('variant_id')->nullable();
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
