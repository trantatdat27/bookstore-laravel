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
        Schema::table('books', function (Blueprint $table) {
            // Thêm cột 'sold' kiểu số nguyên, mặc định là 0
            // Đặt nó đứng ngay sau cột 'stock' cho gọn gàng (nếu bạn muốn)
            $table->integer('sold')->default(0)->after('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Xóa cột 'sold' nếu lỡ muốn rollback lại
            $table->dropColumn('sold');
        });
    }
};