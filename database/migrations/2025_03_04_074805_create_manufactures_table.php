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
        Schema::create('manufactures', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->comment('Название компании');
            $table->string('web', 100)->comment('Сайт компании')->nullable();
            $table->string('adress_loading', 255)->comment('Адрес загрузки')->nullable();
            $table->string('note', 255)->comment('Заметки')->nullable();
            $table->boolean('nottypicalproduct')->comment('Продукция под заказ')->default(false);
            $table->boolean('checkmanufacture')->comment('Проверенный производитель')->default(false);
            $table->boolean('date_contract')->comment('Заключен контракт')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufactures');
    }
};
