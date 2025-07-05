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
             Schema::create('shipping_addresses', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id'); // ID người dùng liên kết
            $table->string('name');
            $table->string('phone');
            $table->string('province');
            $table->string('district');
            $table->string('ward');
            $table->string('address_detail');
            $table->string('address');

            $table->timestamps();

            // Nếu có ràng buộc foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_addresses');
    }
};
