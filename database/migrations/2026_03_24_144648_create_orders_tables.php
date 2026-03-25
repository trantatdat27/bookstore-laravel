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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->string('customer_name')->default('Khách vãng lai');
        $table->decimal('total_amount', 15, 2);
        $table->string('status')->default('pending'); // Chờ xử lý
        $table->timestamps();
    });

    Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->onDelete('cascade');
        $table->string('book_title');
        $table->integer('quantity');
        $table->decimal('price', 15, 2);
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
        Schema::dropIfExists('orders_tables');
    }
};
