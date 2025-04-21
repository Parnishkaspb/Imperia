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
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->string('who')->comment("Кто занимается перевозкой");
            $table->unsignedBigInteger('type_car_id');
            $table->foreign('type_car_id')->references('id')->on('car_types')->onDelete('cascade');
            $table->string('telephone');
            $table->string('email');
            $table->text('note');
            $table->boolean('isWorkEarly')->default(false);
            $table->boolean('isDoc')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carriers');
    }
};
