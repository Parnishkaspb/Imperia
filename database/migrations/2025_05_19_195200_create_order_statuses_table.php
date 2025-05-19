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
            array('id_status' => '0','name' => 'Заявка принята'),
            array('id_status' => '1','name' => 'Передана снабжению'),
            array('id_status' => '2','name' => 'Предварительное КП'),
            array('id_status' => '3','name' => 'Анализ рынка ЗАКОНЧЕН'),
            array('id_status' => '4','name' => 'Предложение отправлено'),
            array('id_status' => '5','name' => 'Счет отправлен'),
            array('id_status' => '6','name' => 'Исполнение заказа'),
            array('id_status' => '7','name' => 'УПД не подписаны'),
            array('id_status' => '8','name' => 'Успешно реализован'),
            array('id_status' => '9','name' => 'Закрыто и не реализовано')
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
