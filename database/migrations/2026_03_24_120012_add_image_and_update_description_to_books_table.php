<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('image')->nullable()->after('price'); // Thêm cột ảnh
            $table->text('description')->change(); // Chuyển mô tả thành kiểu text dài
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->string('description')->change(); // Quay về kiểu string ngắn
        });
    }
};