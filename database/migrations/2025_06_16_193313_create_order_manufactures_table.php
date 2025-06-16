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
        Schema::create('order_manufactures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedBigInteger('manufacture_id');
            $table->foreign('manufacture_id')->references('id')->on('manufactures')->onDelete('cascade');
            $table->unsignedBigInteger('category_id')->nullable()->comment('ID категории');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            $table->text('comment')->nullable();
            $table->integer('price')->nullable();

            $table->unique(['order_id', 'product_id', 'manufacture_id', 'category_id'], 'order_unique_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_manufactures');
    }
};
