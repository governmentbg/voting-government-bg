<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBulPredefinedList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bul_predefined_list', function (Blueprint $table) {
            $table->bigInteger('eik')->primary();
            $table->bigInteger('reg_number');
            $table->timestamp('reg_date');
            $table->string('name', 255);
            $table->string('city', 255);
            $table->string('address', 512);
            $table->string('phone', 40);
            $table->string('status', 30);
            $table->timestamp('status_date')->nullable();
            $table->string('email', 255);
            $table->text('goals');
            $table->text('tools');
            $table->text('description');
            $table->tinyInteger('public_benefits')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bul_predefined_list');
    }
}
