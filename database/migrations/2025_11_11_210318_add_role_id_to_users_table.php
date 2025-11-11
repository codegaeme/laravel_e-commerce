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
        Schema::table('users', function (Blueprint $table) {

            $table->foreignId('role_id')
                ->nullable()
                ->after('email')
                ->constrained('roles')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Phải xóa khóa ngoại trước khi xóa cột
            $table->dropConstrainedForeignId('role_id');

            // 2. Xóa cột (Không cần nếu đã dùng dropConstrainedForeignId, nhưng an toàn hơn)
            // $table->dropColumn('role_id');
        });
    }
};
