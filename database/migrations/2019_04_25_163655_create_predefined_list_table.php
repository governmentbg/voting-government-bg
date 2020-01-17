<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePredefinedListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('predefined_list', function (Blueprint $table) {
            $table->bigInteger('eik')->primary();
            $table->bigInteger('reg_number')->nullable();
            $table->timestamp('reg_date')->nullable();
            $table->string('name', 255);
            $table->string('city', 255)->nullable();
            $table->string('address', 512)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('status', 30)->nullable();
            $table->timestamp('status_date')->nullable();
            $table->string('email', 255)->nullable();
            $table->text('goals')->nullable();
            $table->text('tools')->nullable();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('predefined_list');
    }
}
