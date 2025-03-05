<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropForeign(['manufacture_id']);
            $table->foreign('manufacture_id')->references('id')->on('manufactures')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropForeign(['manufacture_id']);
            $table->foreign('manufacture_id')->references('id')->on('manufactures')->onDelete('restrict');
        });
    }
};
