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
        Schema::create('manufacture_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manufacture_id')->comment('ID производителя');
            $table->foreign('manufacture_id')->references('id')->on('manufactures')->onDelete('cascade');

            $table->unsignedBigInteger('product_id')->comment('ID продукции');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacture_products');
    }
};
