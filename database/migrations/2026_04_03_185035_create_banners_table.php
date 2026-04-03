<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    Schema::create('banners', function (Blueprint $table) {
        $table->id();
        $table->string('image'); // Đường dẫn ảnh
        $table->string('title')->nullable(); // Tiêu đề banner
        $table->string('link')->nullable(); // Link khi click vào banner
        $table->boolean('status')->default(1); // 1: Hiện, 0: Ẩn
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banners');
    }
};
