<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('mime_type');
            $table->integer('message_id')->unsigned()->nullable();
            $table->foreign('message_id')->references('id')->on('messages');
            $table->integer('org_id')->unsigned();
            $table->foreign('org_id')->references('id')->on('organisations');
            $table->integer('voting_tour_id')->unsigned();
            $table->foreign('voting_tour_id')->references('id')->on('voting_tour');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
        });

        DB::unprepared("
            ALTER TABLE files ADD COLUMN data MEDIUMBLOB NOT NULL AFTER name;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
