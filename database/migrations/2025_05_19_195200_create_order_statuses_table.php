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
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        $Order_Statuses = array(
            array('id' => '0','name' => 'Заявка принята'),
            array('id' => '1','name' => 'Передана снабжению'),
            array('id' => '2','name' => 'Предварительное КП'),
            array('id' => '3','name' => 'Анализ рынка ЗАКОНЧЕН'),
            array('id' => '4','name' => 'Предложение отправлено'),
            array('id' => '5','name' => 'Счет отправлен'),
            array('id' => '6','name' => 'Исполнение заказа'),
            array('id' => '7','name' => 'УПД не подписаны'),
            array('id' => '8','name' => 'Успешно реализован'),
            array('id' => '9','name' => 'Закрыто и не реализовано')
        );

        DB::table('order_statuses')->insert($Order_Statuses);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
    }
};
