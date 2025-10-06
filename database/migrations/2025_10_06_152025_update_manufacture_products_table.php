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
        Schema::table('manufacture_products', function (Blueprint $table) {
            $table->unsignedInteger('price')->nullable()->comment("Цена за 1 ед товара");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manufacture_products', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
