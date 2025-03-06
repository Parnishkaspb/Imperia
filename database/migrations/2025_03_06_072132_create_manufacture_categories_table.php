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
        Schema::create('manufacture_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manufacture_id')->comment('ID производителя');
            $table->foreign('manufacture_id')->references('id')->on('manufactures')->onDelete('cascade');

            $table->unsignedBigInteger('category_id')->nullable()->comment('ID категории');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacture_categories');
    }
};
