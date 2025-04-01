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
        Schema::table('manufactures', function (Blueprint $table) {
            $table->string('inn_str', 20)->nullable();
        });

        DB::statement("UPDATE manufactures SET inn_str = LPAD(inn, 10, '0')");

        Schema::table('manufactures', function (Blueprint $table) {
            $table->dropColumn('inn');
        });

        Schema::table('manufactures', function (Blueprint $table) {
            $table->renameColumn('inn_str', 'inn');
        });
    }
};
