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
        Schema::table('manufactures', function (Blueprint $table) {
            $table->unsignedBigInteger('region')->nullable();
            $table->foreign('region')->references('id')->on('federal_dists');

            $table->unsignedBigInteger('city')->nullable();
            $table->foreign('city')->references('id')->on('federal_dists');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manufactures', function (Blueprint $table) {
            $table->dropForeign('manufactures_region_foreign');
            $table->dropForeign('manufactures_city_foreign');
            $table->dropColumn(['region', 'city']);
        });
    }
};
