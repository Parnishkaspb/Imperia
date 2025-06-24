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
        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            $table->string('from')->comment("Откуда")->nullable();
            $table->string('to')->comment("Куда")->nullable();

            $table->integer('buying_price')->comment("За сколько везет человек")->nullable();
            $table->integer('selling_price')->comment("За сколько озвучиваем заказчику")->nullable();

            $table->integer("count")->comment("Кол-во доставок")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_deliveries');
    }
};
