<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedTinyInteger('action');
            $table->unsignedTinyInteger('module');
            $table->integer('object')->unsigned()->nullable();
            $table->integer('voting_tour_id')->unsigned()->nullable();
            $table->foreign('voting_tour_id')->references('id')->on('voting_tour');
            $table->timestamp('occurrence');
            $table->string('ip_address', 15);
            $table->index(['user_id', 'voting_tour_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actions_history');
    }
}
